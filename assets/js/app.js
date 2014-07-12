
$(document).ready(function(){
    var $navLinks,
        $navMenu,
        $header,
        $jsLogo,
        menuVisible,
        $elementHeight,
        menuAim,
        $lyricsLink,
        $creditsLink,
        $lyricsCloseBtn,
        $creditsCloseBtn,
        $shareBtn,
        $socialShare,
        $share;

    $navLinks = $(".js-nav-links li");
    $navMenu = $(".js-nav-menu");
    $header = $(".header");
    $jsLogo = $(".js-logo");
    $lyricsLink = $(".js-lyrics-link");
    $creditsLink = $(".js-credits-link");
    $lyricsCloseBtn = $(".js-lyrics-close");
    $creditsCloseBtn = $(".js-credits-close");
    $shareBtn = $(".js-share");
    $socialShare = $(".js-social-share");
    $share = $(".share");

    //If device is mobile, fit Headings to page
    if ( Modernizr.mq('(max-width: 600px)') ) {
        $(".js-fitText").fitText(0.4);
    }

    function init(){
        //Animation timeline for menu navigation
        menuAnim = new TimelineMax({yoyo: true, smoothChildTiming: true, paused: true});
        
        menuAnim.staggerFromTo($navLinks, 0.8, {x: -80, rotationX: -35, rotationY: 0, rotationZ: 0, transformOrigin: "50% 50%"}, {x: 0, y:0, z: 0, rotationX: 0, rotationY: 0, rotationZ: 0, autoAlpha: 1, ease:Sine.easeOut }, 0.2);

        menuVisible = false;
    }

    //Initialize preload settings
    init();


    $navMenu.on('click', function(){
        if (menuVisible === false ) {
            menuAnim.play();
            if ( Modernizr.mq('(max-width: 600px)') ) {
                $header.addClass('showMobile');
            }

            if ( Modernizr.mq('(max-width: 1000px)') ) {
               toggleLogo(true);
            }

        } else {
            menuAnim.reverse();
            $header.removeClass('showMobile');

            if ( Modernizr.mq('(max-width: 1000px)') ) {
               toggleLogo(false);
            }
        }
        menuVisible = !menuVisible;
    });

    $navLinks.on('mouseover', function(){
        TweenMax.to($navLinks.not(this).find('a'), 0.6, {opacity: 0.1, ease:Sine.easeIn});
        TweenMax.to($(this).find('a'), 0.6, {scale: 1.2, ease:Sine.easeOut});
    }).on('mouseout', function(){
        TweenMax.to($navLinks.find('a'), 2, {opacity: 1, scale: 1, ease:Sine.easeIn});
    });

    function toggleLogo(fade){
        if ( fade === true ) {
            TweenMax.to($jsLogo, 1, {opacity: 0.1, ease:Sine.easeOut});
        } else {
            TweenMax.to($jsLogo, 1, {opacity: 1, ease:Sine.easeOut});
        }
    }

    $lyricsLink.on('click', function(){
        showExtra($(this), "lyrics");
    });

    $lyricsCloseBtn.on('click', function(){
        hideExtra($(this));
    });

    $creditsLink.on('click', function(){
        showExtra($(this), "credits");
    });

    $creditsCloseBtn.on('click', function(){
        hideExtra($(this));
    });

    //Show and hide lyrics via click of lyrics button
    function showExtra($target, $extra) {
        if ($extra === "lyrics" ) {
             $element = $target.parent().parent().siblings(".music-item_lyrics");
        }
        if ($extra === "credits" ) {
             $element = $target.parent().parent().siblings(".music-item_credits");
        }

        //Use hack to hide and view the element, then get its height
        $element.show();
        $elementHeight = $element.outerHeight();
        yPos = $element.offset().top;
        $element.hide();

        console.log(yPos);
        //$("html, body").animate({scrollTop: yPos}, 1000);
        TweenMax.to(window, 1, {scrollTo: {y: yPos}, ease: Cubic.easeInOut});

        setTimeout(function(){
            TweenMax.fromTo($element, 2, {height: 0, display: 'block'}, {height: $elementHeight, autoAlpha: 1, rotationZ: 0, ease: Sine.easeOut});
        }, 1000);
    }

    //Hide lyrics window when close buttons is clicked
    function hideExtra($target) {
        $element = $target.parent();
        $element.slideUp(2000);

    }

    $share.on('mouseenter', function(){
        TweenMax.fromTo($socialShare, 1.4, {x: 50, autoAlpha: 0, display: 'inline-block', z: 0}, {x: 0, autoAlpha: 1, ease: Back.easeInOut});
    }).on('mouseleave', function() {
        TweenMax.fromTo($socialShare, 1.4, {x: 0, autoAlpha: 1}, {x: 50, autoAlpha: 0, display: 'none', z: 0, ease: Back.easeInOut});
    });

    $(".media-items").packery({
        itemSelector: '.media-item',
        isInitLayout: true
    });

});