<?php
// JUXWALLIMAGES_MANAGED_LAYOUT_V1
// JUXWALLIMAGES_LIGHTBOX_GATE_V1
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

$jwiVendor = JPATH_SITE . '/modules/mod_jux_news_wall/tmpl/default.php';
if (!is_file($jwiVendor)) { return; }

// When article Lightbox is disabled, never construct its hidden article copies.
// This local template override survives updates of Joomla and the JUX module.
$jwiRender = static function ($items, $params, $moduleId, $ajaxCategoryId) use ($jwiVendor): void {
    if ((string) $params->get('joomla_lightbox', '1') === '1') {
        include $jwiVendor;
        return;
    }

$activeCategoryId = isset($ajaxCategoryId) ? (int) $ajaxCategoryId : 0;
$total  = modJUXNewsWallHelper::getTotal($params, $activeCategoryId);
$items1 = modJUXNewsWallHelper::getList($params, 0, $total, $activeCategoryId);

$show_date_format   = $params->get('show_date_format', 'Y-m-d H:i:s');
$show_category      = $params->get('show_category', 0);
$show_hits          = $params->get('show_hits', 0);
$show_author        = $params->get('show_author', 0);
$use_link_a         = $params->get('use_link_a', 0);

// Process to get Categories to filter
if($params->get('joomla_filter_by') == "1") {
    $categories = array();
    for($i = 0; $i < count($items1); $i++){
        $category[$i] = new stdClass;
        $category[$i]->title = $items1[$i]->category_title;
        $category[$i]->alias = $items1[$i]->category_alias;
        $category[$i]->id = (int) $items1[$i]->catid;
        if(in_array($category[$i], $categories) == false){
            array_push($categories,$category[$i]);
        }
    }
    sort($categories);
}else{
    $joomla_tags = array();
    for($j = 0; $j < count($items1); $j++){
         foreach ($items1[$j]->tags_new as $key => $tag) {
            $joomla_tags[$key] = new stdClass;           
            $joomla_tags[$key]->title = $tag->title;
                array_push($joomla_tags,$joomla_tags[$key]);
        }
    }
    $joomla_tags_new = array();
    for($i = 0; $i<count($joomla_tags); $i++){
        $joomla_tags[$i]= $joomla_tags[$i]->title;
        if(in_array($joomla_tags[$i], $joomla_tags_new) == false){
            array_push($joomla_tags_new,$joomla_tags[$i]);
        }
    }
    sort($joomla_tags);
}
?>

<div id="news-wall-<?php echo $moduleId; ?>" data-total="<?php echo (int) $total; ?>" class="itemListView<?php if ($params->get('moduleclass_sfx')) echo ' ' . $params->get('moduleclass_sfx'); ?>">
    <?php if($params->get('filter_category') == "1") { 
        if($params->get('joomla_filter_by') == "1") { ?>

            <div class="news-wall-filters clearfix">
                <ul data-option-key="filter" id="filter">
                    <li class="next">
                        <a class="selected " title="<?php echo Text::_('JUX_NEWS_WALL_ALL')?>" data-option-value="*" data-category-id="0" href="#"><?php echo Text::_('JUX_NEWS_WALL_ALL')?></a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li class="next">
                        <a title="<?php echo $cat->title; ?>" data-option-value=".<?php echo $cat->alias; ?>" data-category-id="<?php echo (int) $cat->id; ?>" href="#"><?php echo $cat->title; ?></a>
                    </li>
                    <?php endforeach; ?>
                    <?php if($params->get('filter_search') == "1") : ?>
                    <li class="next search">
                        <input type="text" id="quicksearch-<?php echo $moduleId; ?>" placeholder="<?php echo Text::_('JUX_NEWS_WALL_SEARCH'); ?>" />
                    </li>
                    <?php endif; ?>
                </ul>
                
            </div>
        <?php }else{ ?>

            <div class="news-wall-filters clearfix">
                <ul data-option-key="filter" id="filter">
                    <li class="next">
                        <a class="selected " title="<?php echo Text::_('JUX_NEWS_WALL_ALL')?>" data-option-value="*" href="#"><?php echo Text::_('JUX_NEWS_WALL_ALL')?></a>
                    </li>
                    <?php for ( $i =0; $i < count($joomla_tags_new); $i++){
                        $joomla_tags_new_ = str_replace(" ","_",$joomla_tags_new[$i]);
                    ?>
                    <li class="next">
                        <a title="<?php echo $joomla_tags_new[$i]; ?>" data-option-value=".<?php echo $joomla_tags_new_; ?>" href="#"><?php echo $joomla_tags_new[$i];; ?></a>
                    </li>
                    <?php }?>
                    <?php if($params->get('filter_search') == "1") : ?>
                    <li class="next search">
                        <input type="text" id="quicksearch-<?php echo $moduleId; ?>" placeholder="<?php echo Text::_('JUX_NEWS_WALL_SEARCH'); ?>" />
                    </li>
                    <?php endif; ?>
                </ul>
                
            </div>

        <?php } ?>
    <?php } ?>
    
    <?php if( !empty($items) ) : ?>
    <div id="news-itemList-<?php echo $moduleId; ?>">
        <?php foreach ($items as $item) : 
        $tags="";
        $item_tags = $item->tags_new;

        $urls = $item->urls;
        $urls = json_decode($urls, true);

        if (isset($urls['urla']) && $use_link_a) {
            $item->link = $urls['urla'];
        }
                        
        for($i = 0; $i < count($item->tags_new); $i++){
            $tag_filter = str_replace(" ","_",$item->tags_new[$i]->title);
            $tags= $tags." ".$tag_filter." ";
        }
        ?>
            <div class="wall-item <?php if($params->get('joomla_filter_by') == "1"){ echo $item->category_alias;}else{echo $tags;} ?>" data-category="<?php echo $item->category_alias; ?>">
                <div class="wall-top">
                    <div class="wall-image">
                        <?php $null_image = "{\"image_intro\":\"\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}" ?>
                        <?php if ((($item->images) != $null_image)): ?>
                            <?php
                                $images = json_decode($item->images);
                                $image = '';
                                if (isset($images->image_intro) && !empty($images->image_intro)){
                                    $image = $images->image_intro;
                                } else  if(isset($images->image_fulltext)){
                                    $image = $images->image_fulltext;
                                }

                            ?>
                            <?php if ($params->get('joomla_lightbox') == "1" && $image != ''): ?>
                                <a data-lightbox-type="inline"  class="jux-wall-lightbox"  href="#juxWallLightbox<?php echo $item->id ?>" title="<?php echo $item->title; ?>">
                                    <img src="<?php echo Uri::Base() . $image ?>">
                                </a>
                            <?php endif; ?>
                            <?php if ($params->get('joomla_lightbox') == "0" && $image != ''): ?>
                                <a href="<?php echo $item->link ?>" title="<?php echo $item->title; ?>">
                                    <img src="<?php echo Uri::Base() . $image ?>">
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="wall-bottom clearfix">
                    <div class="item-content">
                        <?php if ($params->get('link_titles') == 1) : ?>
                            <h3 class="catItemTitle">
                                <!-- Use lightbox -->
                                <?php if ($params->get('joomla_lightbox') == "1") { ?>
                                    <a data-lightbox-type="inline"  class="jux-wall-lightbox"  href="#juxWallLightbox<?php echo $item->id ?>" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></a>
                                <?php } else { ?>
                                <!-- Don't use lightbox -->
                                    <a href="<?php echo $item->link ?>"><?php echo $item->title; ?></a>
                                <?php } ?>
                            </h3>
                            <?php if ($params->get('show_date') == 1): ?>
                                <p class="catItemDateCreated">
                                    <?php echo HTMLHelper::_('date', $item->created, $show_date_format); ?>
                                </p>
                            <?php endif; ?>
                        <?php endif ?>
                        <!-- Item introtext -->
                        <?php if ($params->get('show_introtext') == 1) : ?>
                            <div class="catItemIntroText">
                                <?php 
                                    // A card excerpt must not expand an embedded gallery.
                                    $introtext = preg_replace('/\{os-gal-[0-9]+(?:-[0-9]+)?(?:-[0-9]+(?:,[0-9]+)*)?(?:-[a-z]{4,6})?\}/', '', $item->introtext);
                                    $introtext = strip_tags($introtext);
                                    $introtext = substr(htmlspecialchars_decode($introtext), 0, $params->get('introtext_limit', 100));
                                       echo $introtext; 
                                    ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($params->get('show_readmore') == 1) : ?>
                            <div class="jux-readmore clearfix">
                                <a href="<?php echo $item->link ?>" alt="<?php echo $item->title; ?>"><?php echo Text::_('JUX_READMORE'); ?></a>
                            </div>
                        <?php endif; ?>
                        <div class="catItemLinks">
                            <?php if($params->get('joomla_filter_by') == "1") {?>
                                <?php if ($params->get('show_category') == 1 ) { ?>
                                    <!-- Item categories -->
                                    <?php if ($item->featured != 1): ?>
                                        <div class="catItemTagsBlock">
                                            <b><?php echo Text::_('JUX_CATS'); ?>:</b>
                                            <a href="<?php echo $item->displayCategoryLink ?>"><?php echo $item->category_title; ?></a>
                                        </div>
                                    <?php endif; ?>
                                <?php } ?>
                            <?php } else {?>
                                <?php if ($params->get('show_tag') == 1 ) { ?>    
                                    <div class="catItemTagsBlock">
                                        <?php if(count($item_tags) >0) { ?>
                                            <b><?php echo Text::_('JUX_TAGS'); ?>:</b> 
                                        <?php } ?>
                                        <?php for($j = 0; $j < count($item_tags); $j++){ 
                                        $item_tag_link = Uri::base().'index.php/component/tags/tag/'.$item->id.'-'.str_replace(" ","_",$item_tags[$j]->title);
                                        ?>
                                        <a href="<?php echo $item_tag_link; ?>">
                                        <?php 
                                                if($j < count($item_tags)-1){
                                                   echo "#".$item_tags[$j]->title.", ";
                                                }else{
                                                    echo"#".$item_tags[$j]->title;
                                                }
                                        ?>
                                             
                                        </a>
                                        <?php }?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php if ((string) $params->get('joomla_lightbox', '1') === '1'): ?>
                <!-- nivo lightbox -->
                <div id="juxWallLightbox<?php echo $item->id ?>" style="display:none">
                    <div class="lightboxContainer">
                        <div class="itemViewLightBox">
                            <!-- Item lightbox Body -->
                            <div class="itemViewBody">
                                <?php 
                                    $images = json_decode($item->images);
                                    ?>
                                    <?php 
                                        if($params->get('item_images_full')){
                                            $image = '';
                                            if(isset($images->image_fulltext)){
                                                $image = $images->image_fulltext;
                                            } else if (isset($images->image_intro)){
                                                $image = $images->image_intro;
                                            } 
                                        }
                                    ?>

                                    <?php if ($image!='') : ?>
                                        <div class="itemViewContent">
                                            <div class="lb_itemImageBlock">
                                                <img src="<?php echo Uri::Base() . $image ?>" alt="<?php echo $item->title; ?>"/> 
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <div class="lb_itemView" style="<?php if ($image=='') echo 'width: 100%;';?>" >
                                    <!-- Item lightbox header -->
                                    <div class="itemViewHeader"><h2><?php echo $item->title ?></h2></div>
                                    <!-- Item lightbox Info-->
                                    <div class="itemViewInfo">
                                        <!-- item date created -->
                                         <?php if ($params->get('show_date') == 1): ?>
                                            <p class="catItemDateCreated">
                                                <?php echo HTMLHelper::_('date', $item->created, $show_date_format); ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($params->get('show_author') == 1): ?>
                                            <i class="lb_icon-line"></i>
                                            <!-- item author -->
                                            <span class="itemViewAuthor">
                                                <i class="lb_icon-author"></i>
                                                <a rel="author" href=""><?php echo $item->author ?></a>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($params->get('show_hits') == 1): ?>
                                            <i class="lb_icon-line"></i>
                                            <!-- Item Hits -->
                                            <span class="itemViewHits">
                                                <i class="lb_icon-hits"></i>
                                                <?php echo $item->hits ?>
                                                <i class="lb_icon-line"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="lb_itemIntroText">
                                        <?php 
                                        $baseUrl = Uri::root(); // This will get the root URL of your Joomla site

                                        // Use a regular expression to find relative image paths and prepend the base URL
                                        $introtext = preg_replace('/src="images\//', 'src="' . $baseUrl . 'images/', $item->introtext);

                                        echo HTMLHelper::_('content.prepare', $introtext); ?>
                                    </div>
                                    <div class="lb_itemFullText">
                                        <?php 
                                        // Use a regular expression to find relative image paths and prepend the base URL
                                        $fulltext = preg_replace('/src="images\//', 'src="' . $baseUrl . 'images/', $item->fulltext);

                                        echo HTMLHelper::_('content.prepare', $fulltext); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php // endif; ?>
    <div class="items-isotope-loading">
        <div class="loading_spinner_wrapper" style="display: none">
            <div class="cube1"></div>
            <div class="cube2"></div>
            <div class="cube3"></div>
            <div class="cube4"></div>
            <div class="cube5"></div>
        </div>
    </div>
    <?php if ($params->get('show_viewmore')): ?>
        <div class="news-wall-loadmore">
            <a href="#" class="btn btn-primary btn-large btn-loadmore"
                    data-id="<?php echo $moduleId;?>" 
                    data-limit="<?php echo $params->get('count', 6);?>" 
                    data-offset="<?php echo (int)$params->get('ajaxlimit', 3);;?>">

                <span id="loadmore-text"><?php echo Text::_('JUX_VIEW_MORE'); ?></span>
            </a>
        </div>
    <?php endif; ?>
</div>
 
<?php if($params->get('filter-gray') == "1") : ?>
<script>
    !function($){
        // Fix on iOS
        var user_agent = navigator.userAgent.toLowerCase(); // detect the user agent
        var ios_devices = user_agent.match(/(iphone|ipod|ipad)/) ? true : false;
        if(ios_devices != true){
            $("#news-itemList-<?php echo $moduleId; ?> .wall-item").mouseenter(
                function () {
                    $('.wall-item').addClass('filter-gray');
                    $(this).removeClass('filter-gray');
                }
            );

            // Leave hover items
            $('#news-itemList-<?php echo $moduleId; ?>').mouseleave(
                function () {
                    $('.wall-item').removeClass('filter-gray');
                }
            );
        }
        
    }(jQuery);
</script>
<?php endif; ?>

<script>
jQuery("#news-wall-<?php echo $moduleId; ?> .news-wall-filters li a:first").addClass('active');
jQuery('#news-wall-<?php echo $moduleId; ?> .news-wall-filters li a').on('click', function (e) {
    e.preventDefault();
    jQuery('#news-wall-<?php echo $moduleId; ?> .news-wall-filters li a.active').removeClass('active');
    jQuery(this).addClass('active');
});
</script>

<!-- Ajax category filtering + load more -->
<script type="text/javascript">
    !function ($) {
        $(document).ready(function(){
            var qsRegex;
            var buttonFilter;
            var $wall = $('#news-wall-<?php echo $moduleId; ?>');
            var $container = $('#news-itemList-<?php echo $moduleId; ?>');
            var initialCount = <?php echo (int) $params->get('count', 6); ?>;
            var ajaxLimit = <?php echo (int) $params->get('ajaxlimit', 3); ?>;
            var activeCategoryId = 0;
            var activeTotal = <?php echo (int) $total; ?>;

            function initIsotope() {
                $container.imagesLoaded().always(function() {
                    $container.isotope({
                        itemSelector: '.wall-item',
                        filter: function() {
                            var $this = $(this);
                            var searchResult = qsRegex ? $this.text().match(qsRegex) : true;
                            var buttonResult = buttonFilter ? $this.is(buttonFilter) : true;
                            return searchResult && buttonResult;
                        },
                        layoutMode: 'masonry'
                    });
                });
            }

            function updateLoadMore(loadedCount) {
                var $loadMore = $wall.find('.news-wall-loadmore');
                var $button = $loadMore.find('a.btn-loadmore');

                if (!$loadMore.length || !$button.length) {
                    return;
                }

                $button.attr('data-limit', loadedCount);
                $button.attr('data-offset', ajaxLimit);
                $button.attr('data-category-id', activeCategoryId);

                if (activeTotal > loadedCount) {
                    $loadMore.show();
                } else {
                    $loadMore.hide();
                }
            }

            function replaceItemsFromResponse(response) {
                var $temp = $('<div>').html(response);
                var $responseWall = $temp.find('#news-wall-<?php echo $moduleId; ?>');
                var $newItems = $responseWall.find('.wall-item');
                var responseTotal = parseInt($responseWall.attr('data-total'), 10);

                if (!isNaN(responseTotal)) {
                    activeTotal = responseTotal;
                }

                // Destroy the old Isotope instance completely before replacing
                // the DOM. Reusing the old instance here leaves stale masonry
                // coordinates/transforms and breaks the column layout after AJAX.
                if ($container.data('isotope')) {
                    $container.isotope('destroy');
                }

                $container.empty();

                if ($newItems.length) {
                    // Remove any layout styles defensively. The fresh Isotope instance
                    // must calculate every item's position from scratch.
                    $newItems.removeAttr('style');
                    $container.append($newItems);
                }

                // The server has already returned only the selected category,
                // therefore all received items are visible.
                buttonFilter = '*';

                $container.imagesLoaded().always(function() {
                    $container.isotope({
                        itemSelector: '.wall-item',
                        filter: function() {
                            var $this = $(this);
                            var searchResult = qsRegex ? $this.text().match(qsRegex) : true;
                            var buttonResult = buttonFilter ? $this.is(buttonFilter) : true;
                            return searchResult && buttonResult;
                        },
                        layoutMode: 'masonry'
                    });

                    // Force one clean layout after all image dimensions are known.
                    $container.isotope('layout');

                    $wall.find('.jux-wall-lightbox').nivoLightbox();
                    $wall.find('.loading_spinner_wrapper').hide();
                    updateLoadMore($newItems.length);
                });
            }

            initIsotope();
            updateLoadMore($container.find('.wall-item').length);

            // Load more for the currently selected category (or All).
            $wall.on('click', 'a.btn-loadmore', function(e) {
                e.preventDefault();

                var $button = $(this);
                var start = parseInt($button.attr('data-limit'), 10) || 0;
                var offset = parseInt($button.attr('data-offset'), 10) || ajaxLimit;
                var moduleId = parseInt($button.attr('data-id'), 10);

                $wall.find('.loading_spinner_wrapper').show();

                $.ajax({
                    type: 'POST',
                    data: {
                        option: 'com_ajax',
                        module: 'jux_news_wall',
                        limit: start,
                        offset: offset,
                        moduleId: moduleId,
                        category_id: activeCategoryId,
                        format: 'raw'
                    },
                    success: function(response) {
                        var $temp = $('<div>').html(response);
                        var $newItems = $temp.find('#news-wall-<?php echo $moduleId; ?> .wall-item');

                        if ($newItems.length) {
                            $container.isotope('insert', $newItems);
                        }

                        var loadedCount = start + $newItems.length;
                        $button.attr('data-limit', loadedCount);

                        $container.imagesLoaded().always(function() {
                            $container.isotope('layout');
                            $wall.find('.jux-wall-lightbox').nivoLightbox();
                            $wall.find('.loading_spinner_wrapper').hide();
                            updateLoadMore(loadedCount);
                        });
                    },
                    error: function() {
                        $wall.find('.loading_spinner_wrapper').hide();
                    }
                });

                return false;
            });

            $wall.find('.jux-wall-lightbox').nivoLightbox();

            // Category buttons: fetch a fresh top-N set from that category.
            $wall.find('.news-wall-filters').on('click', 'a[data-category-id]', function(e) {
                e.preventDefault();

                var categoryId = parseInt($(this).attr('data-category-id'), 10) || 0;
                activeCategoryId = categoryId;
                $wall.find('.loading_spinner_wrapper').show();

                $.ajax({
                    type: 'POST',
                    data: {
                        option: 'com_ajax',
                        module: 'jux_news_wall',
                        moduleId: <?php echo (int) $moduleId; ?>,
                        category_id: categoryId,
                        filterRequest: 1,
                        count: initialCount,
                        format: 'raw'
                    },
                    success: replaceItemsFromResponse,
                    error: function() {
                        $wall.find('.loading_spinner_wrapper').hide();
                    }
                });
            });

            // Tag mode keeps the original client-side Isotope filtering.
            $wall.find('.news-wall-filters').on('click', 'a:not([data-category-id])', function(e) {
                var value = $(this).attr('data-option-value');
                if (value) {
                    e.preventDefault();
                    buttonFilter = value;
                    $container.isotope();
                }
            });

            // Search remains client-side inside the currently loaded set.
            var $quicksearch = $('#quicksearch-<?php echo $moduleId; ?>').keyup(debounce(function() {
                qsRegex = new RegExp($quicksearch.val(), 'gi');
                $container.isotope();
            }));
        });

        function debounce(fn, threshold) {
            var timeout;
            return function debounced() {
                if (timeout) {
                    clearTimeout(timeout);
                }
                function delayed() {
                    fn();
                    timeout = null;
                }
                setTimeout(delayed, threshold || 100);
            };
        }
    }(jQuery);
</script>
<?php
};

if (!\Joomla\CMS\Plugin\PluginHelper::isEnabled('system', 'juxwallimages')) {
    $jwiRender($items, $params, $moduleId, isset($ajaxCategoryId) ? (int) $ajaxCategoryId : 0);
    return;
}

$jwiPlugin = \Joomla\CMS\Plugin\PluginHelper::getPlugin('system', 'juxwallimages');
$jwiOptions = new \Joomla\Registry\Registry($jwiPlugin->params ?? '{}');
$jwiDoc = \Joomla\CMS\Factory::getApplication()->getDocument();
if (method_exists($jwiDoc, 'getWebAssetManager')) {
    $jwiDoc->getWebAssetManager()->registerAndUseScript('plg_system.juxwallimages', 'plg_system_juxwallimages/images.js', ['version'=>'1.0.0'], ['defer'=>true]);
}

ob_start();
try {
    $jwiRender($items, $params, $moduleId, isset($ajaxCategoryId) ? (int) $ajaxCategoryId : 0);
    $jwiOutput = ob_get_clean();
} catch (\Throwable $e) {
    ob_end_clean();
    throw $e;
}
try {
    require_once JPATH_PLUGINS . '/system/juxwallimages/src/Optimizer.php';
    $jwiProcessor = new \JuxWallImages\Optimizer(JPATH_SITE, \Joomla\CMS\Uri\Uri::root(), (int) $jwiOptions->get('width', 800), (int) $jwiOptions->get('quality', 82));
    echo $jwiProcessor->process($jwiOutput, (bool) $jwiOptions->get('defer_lightbox', 1));
} catch (\Throwable $e) {
    echo $jwiOutput; // Retain the original module if processing is unavailable.
}
