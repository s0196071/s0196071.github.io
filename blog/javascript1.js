$(document).ready(function(){
    $('.multiple').slick({
      infinite: true,
      slidesToShow: 2,
      slidesToScroll: 2,
      dots: false, 
      responsive: [
        {
            breakpoint: 719, 
            settings:{
                slidesToShow: 1, 
                slidesToScroll: 1
            }
        }
      ]
    });
});

