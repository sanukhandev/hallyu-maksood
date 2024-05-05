
    // Date Counting
    $('[data-countdown]').each(function() {
       
        var $this = $(this),
            finalDate = $(this).data('countdown');

        $this.countdown(finalDate, function(event) {
            $this.html(event.strftime('<ul><li><span>%D</span><span>Day</span></li> <li><span>%H</span><span>Hour</span></li> <li><span>%M</span><span>Min</span></li> <li><span>%S</span><span>Sec</span></li></ul>'));
        });
    });

      // Cache jQuery Selector
      var $window = $(window),
      $page_wrapper = $('#page_wrapper'),
    
      $four_carousel = $('.four-carousel')
    

       // Four item slide
       if ($four_carousel.length) {
        $four_carousel.owlCarousel({
            loop: false,
            margin: 1,
            nav: true,
            navText: ["<div class='nav-btn prev-slide'><i class='fas fa-chevron-left'></i><span>Prev</span></div>", "<div class='nav-btn next-slide'><span>Next</span><i class='fas fa-chevron-right'></i></div>"],
            dots: true,
            smartSpeed: 500,
            autoplay: false,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 4
                }
            }
        });
    }