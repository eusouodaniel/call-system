$(function(){
  $(document).ready(function() {

    /**
    *
    * Slider - Slick Slider
    *
    **/
    $('.main-slider').slick({
      dots: true,
      arrows: false,
      infinite: true,
      speed: 500,
      fade: true,
      cssEase: 'linear',
      autoplay: true,
      autoplaySpeed: 2000,
      slidesToShow: 1
    });

    /**
    *
    * Menu Mobile
    *
    **/
    $(function(){
      $('.menu-mobile-toggle, .menu-mobile a').on('click', function(event) {
        $('.menu-mobile').slideToggle();
      });
    }());

    /**
    *
    * Smooth Scrolling
    *
    **/
    $(function() {
      $('.scroll-to').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
          var target = $(this.hash);
          target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
          if (target.length) {
            $('html,body').animate({
              scrollTop: target.offset().top
            }, 1000);
            return false;
          }
        }
      });
    });

    /**
    *
    * Responsive equal height grid
    *
    **/
    $(function() {
      $('.grid-item').responsiveEqualHeightGrid();
    });

  });
}());