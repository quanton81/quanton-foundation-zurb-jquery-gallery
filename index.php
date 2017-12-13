<?php    
    function images()
    {
        $extensions = array('jpeg', 'jpg', 'png');
        $dir = 'C:\Apache24\htdocs\test_php_utf8\foundation\gallery';
        $src = 'http://localhost/test_php_utf8/foundation/gallery';
        $files = scandir($dir);

        foreach ($files as $key => $value)
        {
            if (!in_array(pathinfo($dir.$value, PATHINFO_EXTENSION), $extensions))
            {
                unset($files[$key]);
            }
        }
        
        $images = NULL;
        
        foreach ($files as $key => $value)
        {
            $images[] = $src."/".$value;
        }
        
        return json_encode($images, JSON_FORCE_OBJECT);
    }
?>

<!doctype html>
<html class="no-js" lang="en" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Foundation for Sites</title>
        <link rel="stylesheet" href="css/foundation.css">
        <link rel="stylesheet" href="css/app.css">
        <link rel="stylesheet" href="foundation-icons/foundation-icons.css" />
        <style>
            div#modal-gallery {
                padding: 0;
            }
            div#modal-gallery div.text-center {
                position: relative;
            }
            div#modal-gallery h3 {
                position: absolute;
                top: 1rem;
                left: 1rem;
                opacity: 0.6;
            }
            .column-block {
                padding: 0.1rem;
                margin-bottom: 0;
            }
            button.next {
                position: absolute;
                top: 50%;
                right: 1rem;
            }
            button.prev {
                position: absolute;
                top: 50%;
                left: 1rem;
            }
        </style>
    </head>
    <body>
        <div id="galleria-1"></div>
        <script src="js/vendor/jquery.js"></script>
        <script src="js/vendor/what-input.js"></script>
        <script src="js/vendor/foundation.js"></script>
        <script src="js/app.js"></script>
        <script>
            var imagesGallery = function (id) {
                this.container = null;
                this.galleryImagesJson = JSON.parse('<?php echo images(); ?>');
                this.galleryImages = [];
                this.galleryImagesSrcs = [];
                this.counter;
                this.h2 = 'Galleria Immagini';
                this.h3 = 'Immagine';
                for(var tmp in this.galleryImagesJson) {
                    var img = new Image();
                    img.src = this.galleryImagesJson[tmp];
                    img.hash = tmp;
                    this.galleryImages.push(img);
                    this.galleryImagesSrcs.push(tmp);
                }
                this.galleryImagesNum = this.galleryImages.length;
                this.prevNextShowHide = function (hash) {
                    $("button.prev").show();
                    $("button.next").show();
                    if(parseInt(hash) === 0) {
                        $("button.prev").hide();
                    }
                    else if(parseInt(hash) === (this.galleryImages.length - 1)) {
                        $("button.next").hide();
                    }
                };
                this.init = function () {
                    this.container = $('#' + id);
                    this.container.append('<h2>'+this.h2+'</h2>');
                    var smallUp = 4;
                    var mediumUp = 6;
                    var largeUp = 8;
                    var galleryImagesLength = parseInt(galleryImages.length);
                    if(galleryImagesLength < smallUp) {
                        smallUp = galleryImagesLength;
                    }
                    if(galleryImagesLength < mediumUp) {
                        mediumUp = galleryImagesLength;
                    }
                    if(galleryImagesLength < largeUp) {
                        largeUp = galleryImagesLength;
                    }
                    this.container.append('<div id="div-gallery" class="row expanded small-up-'+smallUp+' medium-up-'+mediumUp+' large-up-'+largeUp+'"></div>');
                    this.galleryImages.forEach(function(element){
                        this.container.find('#div-gallery').append('\
                            <div class="column column-block">\n\
                                <a data-toggle="modal-gallery" href="#'+element.hash+'">\n\
                                    <img src="'+element.src+'" alt="">\n\
                                </a>\n\
                            </div>');
                    });
                    this.container.append('<div class="full reveal" id="modal-gallery" data-reveal><div class="text-center"><h3>'+this.h3+'</h3><img id="gallery-img" src="" alt=""><button class="prev button hollow secondary" type="button"><span aria-hidden="true"><big><i class="fi-minus"></i></big></span></button><button class="next button hollow secondary" type="button"><span aria-hidden="true"><big><i class="fi-plus"></i></big></span></button></div><button class="close-button" data-close aria-label="Close reveal" type="button"><span aria-hidden="true">&times;</span></button></div>');
                    $(document).foundation();
                };
                this.resizeImg = function () {
                    $("img#gallery-img").css('margin-top', '0px');
                    $("button.prev").css('top', '50%');
                    $("button.next").css('top', '50%');
                    if($("img#gallery-img").height() > window.innerHeight) {
                        $("img#gallery-img").height(Math.min(self.galleryImages[self.counter].height, window.innerHeight));
                    }
                    else if($("img#gallery-img").height() < window.innerHeight) {
                        var marginTop = (window.innerHeight - $("img#gallery-img").height()) / 2;
                        $("img#gallery-img").css('margin-top', marginTop+'px');
                        $("button.prev").css('top', 'calc(50% + '+(marginTop / 2)+'px'+')');
                        $("button.next").css('top', 'calc(50% + '+(marginTop / 2)+'px'+')');
                    }
                };
                var self = this;
                $(document).on('click', 'button.prev', {self: self}, function() {
                    hash = self.counter >= 1 ? self.galleryImages[--self.counter] : self.galleryImages[self.counter];
                    var src = hash.src;
                    window.location.hash = hash.hash;
                    $("img#gallery-img").attr("src", src);
                    $("div#modal-gallery h3").text((self.counter + 1) + "° " + self.h3);
                    self.prevNextShowHide(hash.hash);
                });
                $(document).on('click', 'button.next', {self: self}, function() {
                    hash = self.counter < (self.galleryImagesNum - 1) ? self.galleryImages[++self.counter] : self.galleryImages[self.counter];
                    var src = hash.src;
                    window.location.hash = hash.hash;
                    $("img#gallery-img").attr("src", src);
                    $("div#modal-gallery h3").text((self.counter + 1) + "° " + self.h3);
                    self.prevNextShowHide(hash.hash);
                });
                $(document).on('click', 'a[data-toggle="modal-gallery"]', {self: self}, function(event) {
                    event.preventDefault();
                    hash = this.hash.substr(1);
                    self.counter = self.galleryImagesSrcs.indexOf(hash); 
                    var src = self.galleryImages[self.counter].src;
                    window.location.hash = hash;
                    $("img#gallery-img").attr("src", src);
                    $("div#modal-gallery h3").text((self.counter + 1) + "° " + self.h3);
                    self.resizeImg();
                    self.prevNextShowHide(hash);
                });
                $(window).on('resize', {self: self}, function(){
                    self.resizeImg();
                });
                this.init();
            };
            $(document).on("ready", function() {
                imagesGallery('galleria-1');
            });
        </script>
    </body>
</html>
