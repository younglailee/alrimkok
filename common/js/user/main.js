$(function(){

    var ww = window.innerWidth;

    //=============================================== pl-sw
    var pl_sw = undefined;

    function initSwiper() {

    if (ww >= 1280 && pl_sw == undefined) {
        pl_sw = new Swiper(".pl-sw", {
            slidesPerView: 'auto',
            spaceBetween: 18,
            navigation: {
                nextEl: ".pl-next",
                prevEl: ".pl-prev",
            },
        });
    } else if (ww < 1280 && pl_sw != undefined) {
        pl_sw.destroy();
        pl_sw = undefined;
    }
    }

    initSwiper();

    $(window).on('resize', function () {
        ww = window.innerWidth;
        initSwiper();
    });




    //=============================================== sg-sw
    var sg_sw = undefined;

    function initSwiper02() {

    if (ww >= 1280 && sg_sw == undefined) {
        sg_sw = new Swiper(".sg-sw", {
            slidesPerView: 'auto',
            spaceBetween: 18,
            navigation: {
                nextEl: ".sg-next",
                prevEl: ".sg-prev",
            },
        });
    } else if (ww < 1280 && sg_sw != undefined) {
        sg_sw.destroy();
        sg_sw = undefined;
    }
    }

    initSwiper02();

    $(window).on('resize', function () {
        ww = window.innerWidth;
        initSwiper02();
    });



    //=============================================== main-banner
    var mb_sw = new Swiper(".mb-sw", {
        slidesPerView: 1.05,
        spaceBetween: 18,
        centeredSlides: true,
        autoplay: {
        delay: 5000,
        speed: 800,
        disableOnInteraction: false,
        },
        navigation: {
        nextEl: ".mb-next",
        prevEl: ".mb-prev",
        },
        loop:'true',
        breakpoints: {
            568: {
                slidesPerView: 1.5,
            },
            768: {
                slidesPerView: 1.8,
            },
            1024: {
                slidesPerView: 1,
            },
          },
    });


    //=============================================== map
    $(function () {
        $('#map').load('map.html');
    });



    //=============================================== map-dropDown
    $('.map-dropDown li').click(function(){
        var dropTxt = $(this).text().trim();
        $(this).closest('.dropDown').find('.dropBtn').text(dropTxt);
        $(this).closest('.dropDown').removeClass('on');
    });
});
