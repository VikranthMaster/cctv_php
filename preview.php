<?php
require 'vars.php';
require_once('authorize.php');
$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];
$HOUR = $_GET["hour"];
$TAG= $_GET["tag"];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> <?php echo "$CAMERA Photos ($DATE) ($HOUR) (All)" ?> </title>
    <?php echo "<h1><center><a href='../../../'>$CAMERA Photos</a>&nbsp<a href='./photos.php?camera=$CAMERA&date=$DATE'>($DATE)</a> ($HOUR) (All)</a></center></h1>" ?>

    <!-- Core CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.css">

    <!-- Skin CSS file (styling of UI - buttons, caption, etc.)
     In the folder of skin CSS file there are also:
     - .png and .svg icons sprite, 
     - preloader.gif (for browsers that do not support CSS animations) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/default-skin/default-skin.css">

    <!-- Core JS file -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.js"></script>

    <!-- UI JS file -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe-ui-default.min.js"></script>
    <style>
        .my-gallery {
            width: 100%;
            float: left;
        }
        .my-gallery img {
            width: 100%;
            height: auto;
        }
        .my-gallery figure {
            display: block;
            float: left;
            margin: 0 5px 5px 0;
            width: 400px;
        }
        .my-gallery figcaption {
            display: none;
        }

        /* Style tab links */
        .tablink {
            background-color: #555;
            color: white;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            font-size: 17px;
            width: 33.33%;
        }

        .tablink:hover {
            background-color: #777;
        }

        /* Style the tab content (and add height:100% for full page content) */
        .tabcontent {
            display: none;
            padding: 100px 20px;
            height: 100%;
        }
    </style>

</head>
<body>

<?php
$other_cam = $CAMERA=="Gate"? "Stairs" : "Gate";
$int_hour = substr($HOUR,0,2);
$prev_hour = sprintf("%'.02d",$int_hour-1);
$next_hour = sprintf("%'.02d",$int_hour+1);
echo "<h2><h2>";
if($int_hour!="00"){
    echo "<div style='float: left'><a href='./preview.php?camera=$CAMERA&date=$DATE&hour=$prev_hour"."hour&tag=$TAG'> Previous</a> ($prev_hour hour)</div>\n";
}
if($int_hour!="23"){
    echo "<div style='float: right'><a href='./preview.php?camera=$CAMERA&date=$DATE&hour=$next_hour"."hour&tag=$TAG'> Next</a> ($next_hour hour)</div>\n";
}
echo "<div style='margin: auto; width: 250px;'><a href='./video_preview.php?camera=$CAMERA&date=$DATE&hour=$HOUR'>Videos</a>&emsp;&emsp;<a href='./preview.php?camera=$other_cam&date=$DATE&hour=$HOUR&tag=$TAG'>$other_cam</a></div></h2>\n";

echo "<button class='tablink' onclick=\"openPage('Persons', this, 'green')\" id='defaultOpen'>Persons</button>\n";
echo "<button class='tablink' onclick=\"openPage('Other', this, 'green')\">Other</button>\n";
echo "<button class='tablink' onclick=\"openPage('All', this, 'green')\">All</button>\n";

$photo_dir = $CAMERA=="Gate"? $GATE_PHOTO_DIR : $STAIRS_PHOTO_DIR;
$photo_dir .= "/$DATE/$HOUR";
$p_images = getPersonImages($photo_dir);
$o_images = getOtherImages($photo_dir);
$all_images = array_merge($p_images,$o_images);
sort($all_images);

echo "<div id='Persons' class='tabcontent'>\n";
echo "<div class='my-gallery' itemscope itemtype='http://schema.org/ImageGallery'>\n";
foreach($p_images as $index => $img){
    $cam = $CAMERA=="Gate"?"GatePhotos":"StairsPhotos";
    $img_link = "./$cam/$DATE/$HOUR/$img";
    $thumb_link = "./$cam/$DATE/$HOUR/thumbnails/$img";
    if(!file_exists("$HDD_ROOT/$cam/$DATE/$HOUR/thumbnails/$img")){
        $thumb_link = $img_link;
    }
    echo "<figure itemprop='associatedMedia' itemscope itemtype='http://schema.org/ImageObject'>\n";
    echo "<a href='$img_link' itemprop='contentUrl' data-size='1920x1080'> <img src='$thumb_link' itemprop='thumbnail' alt='Image description'/></a>\n";
    echo "<figcaption itemprop='caption description'>$DATE $img</figcaption></figure>\n";
}
echo '</div>';
echo '</div>';


echo "<div id='Other' class='tabcontent'>\n";
echo "<div class='my-gallery' itemscope itemtype='http://schema.org/ImageGallery'>\n";
foreach($o_images as $index => $img){
    $cam = $CAMERA=="Gate"?"GatePhotos":"StairsPhotos";
    $img_link = "./$cam/$DATE/$HOUR/$img";
    $thumb_link = "./$cam/$DATE/$HOUR/thumbnails/$img";
    if(!file_exists("$HDD_ROOT/$cam/$DATE/$HOUR/thumbnails/$img")){
        $thumb_link = $img_link;
    }
    echo "<figure itemprop='associatedMedia' itemscope itemtype='http://schema.org/ImageObject'>\n";
    echo "<a href='$img_link' itemprop='contentUrl' data-size='1920x1080'> <img src='$thumb_link' itemprop='thumbnail' alt='Image description'/></a>\n";
    echo "<figcaption itemprop='caption description'>$DATE $img</figcaption></figure>\n";
}
echo '</div>';
echo '</div>';


echo "<div id='All' class='tabcontent'>\n";
echo "<div class='my-gallery' itemscope itemtype='http://schema.org/ImageGallery'>\n";
foreach($all_images as $index => $img){
    $cam = $CAMERA=="Gate"?"GatePhotos":"StairsPhotos";
    $img_link = "./$cam/$DATE/$HOUR/$img";
    $thumb_link = "./$cam/$DATE/$HOUR/thumbnails/$img";
    if(!file_exists("$HDD_ROOT/$cam/$DATE/$HOUR/thumbnails/$img")){
        $thumb_link = $img_link;
    }
    echo "<figure itemprop='associatedMedia' itemscope itemtype='http://schema.org/ImageObject'>\n";
    echo "<a href='$img_link' itemprop='contentUrl' data-size='1920x1080'> <img src='$thumb_link' itemprop='thumbnail' alt='Image description'/></a>\n";
    echo "<figcaption itemprop='caption description'>$DATE $img</figcaption></figure>\n";
}
echo '</div>';
echo '</div>';

?>

<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

    <!-- Background of PhotoSwipe. 
         It's a separate element, as animating opacity is faster than rgba(). -->
    <div class="pswp__bg"></div>

    <!-- Slides wrapper with overflow:hidden. -->
    <div class="pswp__scroll-wrap">

        <!-- Container that holds slides. PhotoSwipe keeps only 3 slides in DOM to save memory. -->
        <!-- don't modify these 3 pswp__item elements, data is added later on. -->
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>

        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
        <div class="pswp__ui pswp__ui--hidden">

            <div class="pswp__top-bar">

                <!--  Controls are self-explanatory. Order can be changed. -->

                <div class="pswp__counter"></div>

                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                <button class="pswp__button pswp__button--share" title="Share"></button>
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                <!-- Preloader demo https://codepen.io/dimsemenov/pen/yyBWoR -->
                <!-- element will get class pswp__preloader--active when preloader is running -->
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>

            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
            </button>

            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
            </button>

            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>

        </div>

    </div>

</div>


<script>
    function openPage(pageName,elmnt,color) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].style.backgroundColor = "";
        }
        document.getElementById(pageName).style.display = "block";
        elmnt.style.backgroundColor = color;
    }


    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();

    var initPhotoSwipeFromDOM = function(gallerySelector) {
        //var options = { loop: false //stop looping };


        // parse slide data (url, title, size ...) from DOM elements
        // (children of gallerySelector)
        var parseThumbnailElements = function(el) {
            var thumbElements = el.childNodes,
                numNodes = thumbElements.length,
                items = [],
                figureEl,
                linkEl,
                size,
                item;

            for(var i = 0; i < numNodes; i++) {

                figureEl = thumbElements[i]; // <figure> element

                // include only element nodes
                if(figureEl.nodeType !== 1) {
                    continue;
                }

                linkEl = figureEl.children[0]; // <a> element

                size = linkEl.getAttribute('data-size').split('x');

                // create slide object
                item = {
                    src: linkEl.getAttribute('href'),
                    w: parseInt(size[0], 10),
                    h: parseInt(size[1], 10)
                };



                if(figureEl.children.length > 1) {
                    // <figcaption> content
                    item.title = figureEl.children[1].innerHTML;
                }

                if(linkEl.children.length > 0) {
                    // <img> thumbnail element, retrieving thumbnail url
                    item.msrc = linkEl.children[0].getAttribute('src');
                }

                item.el = figureEl; // save link to element for getThumbBoundsFn
                items.push(item);
            }

            return items;
        };

        // find nearest parent element
        var closest = function closest(el, fn) {
            return el && ( fn(el) ? el : closest(el.parentNode, fn) );
        };

        // triggers when user clicks on thumbnail
        var onThumbnailsClick = function(e) {
            e = e || window.event;
            e.preventDefault ? e.preventDefault() : e.returnValue = false;

            var eTarget = e.target || e.srcElement;

            // find root element of slide
            var clickedListItem = closest(eTarget, function(el) {
                return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
            });

            if(!clickedListItem) {
                return;
            }

            // find index of clicked item by looping through all child nodes
            // alternatively, you may define index via data- attribute
            var clickedGallery = clickedListItem.parentNode,
                childNodes = clickedListItem.parentNode.childNodes,
                numChildNodes = childNodes.length,
                nodeIndex = 0,
                index;

            for (var i = 0; i < numChildNodes; i++) {
                if(childNodes[i].nodeType !== 1) {
                    continue;
                }

                if(childNodes[i] === clickedListItem) {
                    index = nodeIndex;
                    break;
                }
                nodeIndex++;
            }



            if(index >= 0) {
                // open PhotoSwipe if valid index found
                openPhotoSwipe( index, clickedGallery );
            }
            return false;
        };

        // parse picture index and gallery index from URL (#&pid=1&gid=2)
        var photoswipeParseHash = function() {
            var hash = window.location.hash.substring(1),
                params = {};

            if(hash.length < 5) {
                return params;
            }

            var vars = hash.split('&');
            for (var i = 0; i < vars.length; i++) {
                if(!vars[i]) {
                    continue;
                }
                var pair = vars[i].split('=');
                if(pair.length < 2) {
                    continue;
                }
                params[pair[0]] = pair[1];
            }

            if(params.gid) {
                params.gid = parseInt(params.gid, 10);
            }

            return params;
        };

        var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
            var pswpElement = document.querySelectorAll('.pswp')[0],
                gallery,
                options,
                items;

            items = parseThumbnailElements(galleryElement);

            // define options (if needed)
            options = {
                loop: false,
                // define gallery index (for URL)
                galleryUID: galleryElement.getAttribute('data-pswp-uid'),

                getThumbBoundsFn: function(index) {
                    // See Options -> getThumbBoundsFn section of documentation for more info
                    var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
                        pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                        rect = thumbnail.getBoundingClientRect();

                    return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
                }

            };

            // PhotoSwipe opened from URL
            if(fromURL) {
                if(options.galleryPIDs) {
                    // parse real index when custom PIDs are used
                    // http://photoswipe.com/documentation/faq.html#custom-pid-in-url
                    for(var j = 0; j < items.length; j++) {
                        if(items[j].pid == index) {
                            options.index = j;
                            break;
                        }
                    }
                } else {
                    // in URL indexes start from 1
                    options.index = parseInt(index, 10) - 1;
                }
            } else {
                options.index = parseInt(index, 10);
            }

            // exit if index not found
            if( isNaN(options.index) ) {
                return;
            }

            if(disableAnimation) {
                options.showAnimationDuration = 0;
            }

            // Pass data to PhotoSwipe and initialize it
            gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
            gallery.init();
        };

        // loop through all gallery elements and bind events
        var galleryElements = document.querySelectorAll( gallerySelector );

        for(var i = 0, l = galleryElements.length; i < l; i++) {
            galleryElements[i].setAttribute('data-pswp-uid', i+1);
            galleryElements[i].onclick = onThumbnailsClick;
        }

        // Parse URL and open gallery if it contains #&pid=3&gid=1
        var hashData = photoswipeParseHash();
        if(hashData.pid && hashData.gid) {
            openPhotoSwipe( hashData.pid ,  galleryElements[ hashData.gid - 1 ], true, true );
        }
    };

    // execute above function
    initPhotoSwipeFromDOM('.my-gallery');

</script>
</body>
</html>
