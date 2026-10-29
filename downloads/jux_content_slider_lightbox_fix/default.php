<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Tags\Site\Helper\RouteHelper;

$linkAvatar 	= Uri::base();
$site_url 		= "https://" . $_SERVER['HTTP_HOST'];
$style_lightbox = $params->get('style_lightbox','fadeScale');
$item_style 	= $params->get('item_style','1');
$jux_margin 	= $params->get('jux_margin','5');
$height_image 	= $params->get('height_image','250px');

// A card excerpt must not expand an embedded gallery. Do not change the
// article object: the original introtext remains available in Lightbox mode.
$jcsCardExcerpt = static function ($description, $limit): string {
	$description = htmlspecialchars_decode((string) $description);
	$description = preg_replace('/\{os-gal-[0-9]+(?:-[0-9]+)?(?:-[0-9]+(?:,[0-9]+)*)?(?:-[a-z]{4,6})?\}/', '', $description);
	return substr($description, 0, (int) $limit);
};
?>

<div class="jux-content-slider">
	<div id="jux-content-slider<?php echo $module->id; ?>" class="layout_<?php echo $item_style;?> owl-carousel jux-content-slider-wrapper">
    <?php foreach ($lists as $list): 
    	$tags = new TagsHelper;
		$tags = $tags->getItemTags('com_content.article', $list->id);
		if ($list->image == ''){
			$image = Uri::root() . 'modules/mod_jux_content_slider/assets/images/placeholder.jpg';
		} else {
			$image = $list->image;
		}
    ?>
	    <div class="item clearfix"> 
	        <?php if ($params->get('show_image') == 1): ?>
            <div class="jux-thumbnail clearfix">
                <figure class="item-hover-effect <?php echo $params->get('select_hover_effects')?>">
                    <div class="entry-thumbnail-bg" style="background-image: url(<?php echo $image;?>);"></div>
					<?php if($params->get('show_text_image')==1 && $item_style != 7) {?>
						<figcaption>
							<h4 class="title">
								<?php echo $list->category_title; ?>
							</h4>   
							<div class="line"></div>
							<div class="sub-title">
								<?php echo $list->title; ?>
							</div>
						</figcaption>
					<?php } ?>
				</figure>
				<?php 
        			if ($params->get('linked_image') == 1) {
        				if ($params->get('Show_light_box') == 0) {
        					echo '<a class="item-link" href="#juxlightbox'.$list->id.'" data-lightbox-gallery="gallery1" data-lightbox-type="inline"></a>';
        				} else {
        					echo '<a class="item-link" href="'.$list->link.'"></a>';
        				}
        			}
            	?>
			</div>
		<?php endif ?>	  
			<div class="jux-content-wrapper clearfix"> 
				<div class="jux-content-inner">
					<?php if ( $item_style == '1') : ?>
						<?php if ($params->get('date', 'false') == 1): ?>
							<div class="jux-date">
								<?php
								if (($params->get('custom_time')) == '') {
									$ft = $params->get('formattime');
								} else {
									$ft = $params->get('custom_time');
								}
								$date = new DateTime($list->created);
								
								$timef = $date->format($ft);
								echo $timef;
								?>  
							</div>
						<?php endif; ?>
						<?php if ($params->get('show_title', 1) == 1): ?>
							<h2 class="jux-title">
								<?php if ($params->get('linked_title', 1) == 1) { 
									echo '<a href="'.$list->link.'" title="'.$list->title.'">'.$list->title.'</a>';
								} else { 
									echo $list->title;
								} ?>
							</h2>
						<?php endif ?>

						<?php if ($params->get('show_desc', 1) && $list->description != ''): ?>
							<div class= "jux-description">
								<?php $tt = $jcsCardExcerpt($list->description, $params->get('description_max_chars', 100));
									echo $tt; ?> 
							</div>
						<?php endif; ?> 

						<?php if ( $params->get('show_tags', 'false') == 1): ?> 
							<?php if (!empty($tags)) : ?>
								<ul class="tags list-inline">
									<?php foreach ($tags as $i => $tag) : ?>
											<li class="list-inline-item tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>" itemprop="keywords">
												<a href="<?php echo Route::_(RouteHelper::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>" class="btn btn-sm">
													<?php echo ("#".$tag->title); ?>
												</a>
											</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						<?php endif; ?>

						<div class="share_like">
							<?php if ($params->get('Show_Readmore') == 1 or ( $params->get('share') == 1)) : ?> 
								<div class="jux-content-slider-share">

									<?php if ($params->get('share') == 1): $link_share = $site_url . $list->link; ?>
										<div class="jux-social">
											<a class="fa fa-share-alt"></a>
											<ul class="jux-social-share" >
												<li>
													<div class="facebook">
														<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $link_share; ?>" target="_blank">
															<i class="fa fa-facebook"></i>
														</a>
													</div>
												</li>                                   
												<li>
													<div class="twitter">
														<a target="_blank" href="https://twitter.com/share?u=<?php echo $link_share; ?>" onclick="window.open('http://twitter.com/share', '', 'width=680,height=480,scrollbars=no,resizable=no,location=no,menubar=no,toolbar=no')"><i class="fa fa-twitter"></i></a>
													</div>
												</li>                                   
												<li>
													<div class="linkedin">
														<a class="hint--top linkedin" onclick="popUp=window.open('https://www.linkedin.com/sharing/share-offsite/url=<?php echo $link_share; ?>','sharer','scrollbars=yes,width=800,height=400');popUp.focus();return false;" href="javascript:;"><i class="fa fa-linkedin"></i></a>
													</div>
												</li>                               
											</ul>                             
										</div>  
									<?php endif ?>
									<?php if ($params->get('Show_Readmore') == 1) { ?>          
										<a class="jux-read-more" href="<?php echo $list->link ?>"><i class="fa fa-ellipsis-h"></i></a>
									<?php } ?>  
								</div>  
							<?php endif ?>
						</div>
					<?php endif; ?>

					<?php if ( $item_style == '2') : ?>
						<?php if ($params->get('date', 'false') == 1): ?>
							<div class="jux-date">
								<?php
								if (($params->get('custom_time')) == '') {
									$ft = $params->get('formattime');
								} else {
									$ft = $params->get('custom_time');
								}
								$date = new DateTime($list->created);
								
								echo '<span class="post-day">'.$date->format('d').'</span>';
								echo '<span class="post-month">'.$date->format('M').'</span>';
								?>  
							</div>
						<?php endif; ?>
						<?php if ($params->get('show_title', 1) == 1): ?>
							<h2 class="jux-title">
								<?php if ($params->get('linked_title', 1) == 1) { 
									echo '<a href="'.$list->link.'" title="'.$list->title.'">'.$list->title.'</a>';
								} else { 
									echo $list->title;
								} ?>
							</h2>
						<?php endif ?>

						<?php if ($params->get('show_desc', 1) && $list->description != ''): ?>
							<div class= "jux-description">
								<?php $tt = $jcsCardExcerpt($list->description, $params->get('description_max_chars', 100));
									echo $tt; ?> 
							</div>
						<?php endif; ?> 

						<?php if ( $params->get('show_tags', 'false') == 1): ?> 
							<?php if (!empty($tags)) : ?>
								<ul class="tags list-inline">
									<?php foreach ($tags as $i => $tag) : ?>
											<li class="list-inline-item tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>" itemprop="keywords">
												<a href="<?php echo Route::_(RouteHelper::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>" class="btn btn-sm">
													<?php echo ("#".$tag->title); ?>
												</a>
											</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						<?php endif; ?>
						<div class="entry-meta-more">
							<div class="author-avatar"><i class="fa fa-user"></i></div>
							<h6><?php echo $list->author;?></h6>
						</div>
					<?php endif; ?>

					<?php if ( $item_style == '3') : ?>
						<div class="entry-meta-category">
                    		<span><?php  echo $list->category_title; ?></span>              
                    	</div>
						<?php if ($params->get('show_title', 1) == 1): ?>
							<h2 class="jux-title">
								<?php if ($params->get('linked_title', 1) == 1) { 
									echo '<a href="'.$list->link.'" title="'.$list->title.'">'.$list->title.'</a>';
								} else { 
									echo $list->title;
								} ?>
							</h2>
						<?php endif ?>

						<?php if ($params->get('show_desc', 1) && $list->description != ''): ?>
							<div class= "jux-description">
								<?php $tt = $jcsCardExcerpt($list->description, $params->get('description_max_chars', 100));
									echo $tt; ?> 
							</div>
						<?php endif; ?> 

						<?php if ( $params->get('show_tags', 'false') == 1): ?> 
							<?php if (!empty($tags)) : ?>
								<ul class="tags list-inline">
									<?php foreach ($tags as $i => $tag) : ?>
											<li class="list-inline-item tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>" itemprop="keywords">
												<a href="<?php echo Route::_(RouteHelper::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>" class="btn btn-sm">
													<?php echo ("#".$tag->title); ?>
												</a>
											</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ($params->get('date', 'false') == 1): ?>
							<div class="jux-date">
								<?php
								if (($params->get('custom_time')) == '') {
									$ft = $params->get('formattime');
								} else {
									$ft = $params->get('custom_time');
								}
								$date = new DateTime($list->created);
								$timef = $date->format($ft);
								echo $timef;
								?>  

							</div>
						<?php endif; ?>
					<?php endif; ?>
					
					<?php if ( $item_style == '4') : ?>
						<?php if ($params->get('date', 'false') == 1): ?>
							<div class="jux-date">
								<?php
								if (($params->get('custom_time')) == '') {
									$ft = $params->get('formattime');
								} else {
									$ft = $params->get('custom_time');
								}
								$date = new DateTime($list->created);
								
								$timef = $date->format($ft);
								echo $timef;
								?>  
							</div>
						<?php endif; ?>
						<?php if ($params->get('show_title', 1) == 1): ?>
							<h2 class="jux-title">
								<?php if ($params->get('linked_title', 1) == 1) { 
									echo '<a href="'.$list->link.'" title="'.$list->title.'">'.$list->title.'</a>';
								} else { 
									echo $list->title;
								} ?>
							</h2>
						<?php endif ?>

						<?php if ($params->get('show_desc', 1) && $list->description != ''): ?>
							<div class= "jux-description">
								<?php $tt = $jcsCardExcerpt($list->description, $params->get('description_max_chars', 100));
									echo $tt; ?> 
							</div>
						<?php endif; ?> 

						<?php if ( $params->get('show_tags', 'false') == 1): ?> 
							<?php if (!empty($tags)) : ?>
								<ul class="tags list-inline">
									<?php foreach ($tags as $i => $tag) : ?>
											<li class="list-inline-item tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>" itemprop="keywords">
												<a href="<?php echo Route::_(RouteHelper::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>" class="btn btn-sm">
													<?php echo ("#".$tag->title); ?>
												</a>
											</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($params->get('Show_Readmore') == 1) { ?>
							<div class="post-read-more"> 
								<a href="<?php echo $list->link;?>"> 
									<span class="btn-text"> Read More </span> 
									<i class="fa fa-long-arrow-right"></i> 
								</a>
							</div>
						<?php } ?>
					<?php endif; ?>

					<?php if ( $item_style == '5') : ?>
						<div class="entry-meta-category">
                    		<span><?php  echo $list->category_title; ?></span>              
                    	</div>
						<?php if ($params->get('show_title', 1) == 1): ?>
							<h2 class="jux-title">
								<?php if ($params->get('linked_title', 1) == 1) { 
									echo '<a href="'.$list->link.'" title="'.$list->title.'">'.$list->title.'</a>';
								} else { 
									echo $list->title;
								} ?>
							</h2>
						<?php endif ?>

						<?php if ($params->get('show_desc', 1) && $list->description != ''): ?>
							<div class= "jux-description">
								<?php $tt = $jcsCardExcerpt($list->description, $params->get('description_max_chars', 100));
									echo $tt; ?> 
							</div>
						<?php endif; ?> 

						<?php if ( $params->get('show_tags', 'false') == 1): ?> 
							<?php if (!empty($tags)) : ?>
								<ul class="tags list-inline">
									<?php foreach ($tags as $i => $tag) : ?>
											<li class="list-inline-item tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>" itemprop="keywords">
												<a href="<?php echo Route::_(RouteHelper::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>" class="btn btn-sm">
													<?php echo ("#".$tag->title); ?>
												</a>
											</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						<?php endif; ?>
						<div class="post-meta post-meta-other">
							<?php if ($params->get('date', 'false') == 1): ?>
								<span class="date">
									<?php
									if (($params->get('custom_time')) == '') {
										$ft = $params->get('formattime');
									} else {
										$ft = $params->get('custom_time');
									}
									$date = new DateTime($list->created);
									$timef = $date->format($ft);
									echo $timef;
									?>  

								</span>
							<?php endif; ?>
							<span class="author"><?php echo Text::_('MOD_JUX_CONTENT_SLIDER_BY'); ?> <?php echo $list->author;?></span>
						</div>
					<?php endif; ?>

					<?php if ( $item_style == '6') : ?>
						<div class="post-meta post-meta-other">
							<?php if ($params->get('date', 'false') == 1): ?>
								<span class="date">
									<?php
									if (($params->get('custom_time')) == '') {
										$ft = $params->get('formattime');
									} else {
										$ft = $params->get('custom_time');
									}
									$date = new DateTime($list->created);
									$timef = $date->format($ft);
									echo $timef;
									?>  

								</span>
							<?php endif; ?>
							<span class="author"><?php echo Text::_('MOD_JUX_CONTENT_SLIDER_BY'); ?> <?php echo $list->author;?></span>
						</div>

						<?php if ($params->get('show_title', 1) == 1): ?>
							<h2 class="jux-title">
								<?php if ($params->get('linked_title', 1) == 1) { 
									echo '<a href="'.$list->link.'" title="'.$list->title.'">'.$list->title.'</a>';
								} else { 
									echo $list->title;
								} ?>
							</h2>
						<?php endif ?>

						<?php if ($params->get('show_desc', 1) && $list->description != ''): ?>
							<div class= "jux-description">
								<?php $tt = $jcsCardExcerpt($list->description, $params->get('description_max_chars', 100));
									echo $tt; ?> 
							</div>
						<?php endif; ?> 

						<?php if ( $params->get('show_tags', 'false') == 1): ?> 
							<?php if (!empty($tags)) : ?>
								<ul class="tags list-inline">
									<?php foreach ($tags as $i => $tag) : ?>
											<li class="list-inline-item tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>" itemprop="keywords">
												<a href="<?php echo Route::_(RouteHelper::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>" class="btn btn-sm">
													<?php echo ("#".$tag->title); ?>
												</a>
											</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($params->get('Show_Readmore') == 1) { ?>
							<div class="post-read-more"> 
								<a href="<?php echo $list->link;?>"> 
									<span class="btn-text"> Read More </span> 
									<i class="fa fa-long-arrow-right"></i> 
								</a>
							</div>
						<?php } ?>
					<?php endif; ?>

					<?php if ( $item_style == '7') : ?>
						<div class="entry-meta-category">
                    		<span><?php  echo $list->category_title; ?></span>              
                    	</div>
						<?php if ($params->get('show_title', 1) == 1): ?>
							<h2 class="jux-title">
								<?php if ($params->get('linked_title', 1) == 1) { 
									echo '<a href="'.$list->link.'" title="'.$list->title.'">'.$list->title.'</a>';
								} else { 
									echo $list->title;
								} ?>
							</h2>
						<?php endif ?>

						<?php if ($params->get('show_desc', 1) && $list->description != ''): ?>
							<div class= "jux-description">
								<?php $tt = $jcsCardExcerpt($list->description, $params->get('description_max_chars', 100));
									echo $tt; ?> 
							</div>
						<?php endif; ?> 

						<div class="post-meta post-meta-other">
							<?php if ($params->get('date', 'false') == 1): ?>
								<span class="date">
									<?php
									if (($params->get('custom_time')) == '') {
										$ft = $params->get('formattime');
									} else {
										$ft = $params->get('custom_time');
									}
									$date = new DateTime($list->created);
									$timef = $date->format($ft);
									echo $timef;
									?>  

								</span>
							<?php endif; ?>
							<span class="author"><?php echo Text::_('MOD_JUX_CONTENT_SLIDER_BY'); ?> <?php echo $list->author;?></span>
						</div>
					<?php endif; ?>
                </div>
            </div> 

			<?php if ($params->get('Show_light_box') == 0): ?>
            <div id="juxlightbox<?php if(isset($list->id)){echo $list->id; }?>" style="display:none">
				<div class="lightboxContainer">
                    <div class="itemViewLightBox">
                        <!-- Item lightbox Body -->
                        <div class="itemViewBody">
                        	<?php if ( $list->image): ?>
                            <div class="itemViewContent">
                                <div class="lb_itemImageBlock">
                                    <img class="lazyOwl" src="<?php echo $image ?>" alt="<?php echo $list->title; ?>" title="<?php echo $list->title; ?>"/>	
                                </div>
                            </div>
                            <?php endif;?>
                            <div class="lb_itemView">
                                <!-- Item lightbox header -->
                                <div class="itemViewHeader">
                                	<h2 class="jux-title"><a href="<?php echo $list->link ?>">
                                		<?php echo $list->title ?></a>
                                	</h2>
                                </div>
                                <div class="lb_itemFullText">
                                    <?php if(isset( $list->introtext)) { echo $list->introtext; } ?>
                                </div>
                            </div>
                        </div>
                        <div class="itemViewContentFooter">
                            <div class="itemSocialSharing"></div>
                        </div>
                    </div>
                </div>
			</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	</div>
	<!-- JS image height -->
	<script type="text/javascript">
	    jQuery(document).ready(function($) {
			<?php if ($params->get('Show_light_box') == 0): ?>
				function initializeLightbox() {
					$('#jux-content-slider<?php echo $module->id; ?> a').nivoLightbox({
						effect:'<?php echo $style_lightbox;?>', // The effect to use when showing the lightbox
						theme: 'default', // The lightbox theme to use
						keyboardNav: true, // Enable/Disable keyboard navigation (left/right/escape)
						clickOverlayToClose: true, // If false clicking the "close" button will be the only way 
					});
				}

				// Initialize lightbox on page load
				initializeLightbox();
			<?php endif; ?>    	
	        $("#jux-content-slider<?php echo $module->id; ?>").owlCarousel({
	        	items: <?php echo $params->get('visible', 2); ?>,
	         	mouseDrag: <?php echo ($params->get('mouse_drag') == 1) ? 'true' : 'false'; ?>,
	         	rtl: <?php echo ($params->get('jux_rtl') == 1) ? 'true' : 'false'; ?>,
	         	autoplay: <?php echo ($params->get('autorun') == 1) ? 'true' : 'false'; ?>,
	         	autoHeight: false,
	         	autoplayTimeout: <?php echo $params->get('animation_speed'); ?>,
	         	autoplaySpeed: <?php echo $params->get('animation_interval'); ?>,
	         	autoplayHoverPause: <?php echo ($params->get('stop0nHover') == 1) ? 'true' : 'false'; ?>,

	         	loop: <?php echo ($params->get('jux_loop') == 1) ? 'true' : 'false'; ?>,

	      		navText: ["<i class=\"fa fa-angle-left \"></i>", "<i class=\"fa fa-angle-right \"></i>"],
	       		nav: <?php echo ($params->get('jux_navigation') == 1) ? 'true' : 'false'; ?>,
	       		dots: <?php echo ($params->get('jux_dots') == 1) ? 'true' : 'false'; ?>,
	       		navSpeed: <?php echo $params->get('animation_interval', 500) ?>,
	       		dotsSpeed: <?php echo $params->get('animation_interval', 500) ?>,
	       		slideTransition: 'linear',
	       		responsive: {
	           		0: {
	               		items: <?php echo $params->get('items_mobile', 1); ?>
	           		},
	           		768: {
	               		items: <?php echo $params->get('items_tablet', 2); ?>
	           		},
	           		991: {
	               		items: <?php echo $params->get('items_desktop_small', 2); ?>
	           		},
	           		1200: {
	               		items: <?php echo $params->get('items_desktop', 2); ?>
	           		},
	           		1400: {
	               		items: <?php echo $params->get('visible', 2); ?>
	           		},
	       		},
	       		onTranslated: function(event) {
					<?php if ($params->get('Show_light_box') == 0): ?>
						initializeLightbox();
					<?php endif; ?>
				}
			});
	    });
	</script>
	<?php
	$style='';
	$style.='#jux-content-slider'.$module->id.' .item-hover-effect{
		height:'.$height_image.';
	}';
	$style.='#jux-content-slider'.$module->id.'  .item{
		margin:'.$jux_margin.'px;
	}';
	$document = Factory::getDocument();
	$document->addStyleDeclaration($style);// add style css

	?>
</div>
