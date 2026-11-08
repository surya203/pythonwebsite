$('.SlickSlider').slick({
  dots: false,
  infinite: true,
  speed: 500,
  autoplay: true,
  autoplaySpeed: 1000,
  slidesToShow: 5,
  slidesToScroll: 2,
  prevArrow: '<button class="slide-arrow prev-arrow"><i class="fa fa fa-angle-left"></i></button>',
  nextArrow: '<button class="slide-arrow next-arrow"><i class="fa fa fa-angle-right"></i></button>',
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3,
        infinite: true,
        dots: true
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
  ]
});

//custom video
$(document).on('click','.js-videoPoster',function(e) {
  e.preventDefault();
  var poster = $(this);
  var wrapper = poster.closest('.js-videoWrapper');
  videoPlay(wrapper);
});

function videoPlay(wrapper) {
  var iframe = wrapper.find('.js-videoIframe');
  var src = iframe.data('src');
  wrapper.addClass('videoWrapperActive');
  iframe.attr('src',src);
}

//testimonials
