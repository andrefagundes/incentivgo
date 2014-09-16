/*jshint forin:true, noarg:true, noempty:true, eqeqeq:true, bitwise:true,
 strict:true, curly:true, browser:true, jquery:true, maxerr:50 */
//smooth scroll on page
$(document).ready(function() {
  'use strict';

  $('.navbar a, .navbar li a, .brand, #footer li a, .more a, a.go-top').on('click', function(event) {
    var $anchor = $(this),
    scrollVal = $($anchor.attr('href')).offset().top - 60;

    if (scrollVal < 0) {
      scrollVal = 0;
    }

    $('[data-spy="scroll"]').each(function() {
      $(this).scrollspy('refresh');
    });

    $.scrollTo(scrollVal, {
      easing: 'easeInOutExpo',
      duration: 1500
    });

    event.preventDefault();
  });

  //responsive embed videos
  $('.video').fitVids();

  //custom scrollbar
  $('html, .modal').niceScroll({
    scrollspeed: 60,
    mousescrollstep: 35,
    cursorwidth: 10,
    cursorborder: 0,
    cursorcolor: '#e3e4e5',
    cursorborderradius: '3px',
    autohidemode: false,
    horizrailenabled: false
  });
  
  //custom scrollbar
  $('.scrollbar').niceScroll({
    cursorwidth: 10,
    cursorborder: 0,
    cursorcolor: '#e3e4e5',
    cursorborderradius: '3px',
    autohidemode: true,
    horizrailenabled: false
  });

  //make intro carousel height of window
  $('#carousel_intro .item').css({'height': ($(window).height()) + 'px'});
  $(window).resize(function() {
    $('#carousel_intro .item').css({'height': ($(window).height()) + 'px'});
  });

  //carousel swipe and autoslide
  var carousels = [
    'intro',
    'content',
    'modal',
    'header_1',
    'header_3'
  ];

  $.each(carousels, function() {
    var suffix = Array.prototype.slice.call(this).join(''),
    element = '#carousel_' + suffix;
    $(element).carousel({
      interval: 0,
      pause: false
    });
    jQuery(element).touchwipe({
      wipeLeft: function() { jQuery(element).carousel('next'); },
      wipeRight: function() { jQuery(element).carousel('prev'); },
      min_move_x: 20,
      preventDefaultEvents: false
    });
  });

  //animated elements
  if ($('.no-touch').length) {
    skrollr.init({
      edgeStrategy: 'set',
      easing: {
        WTF: Math.random,
        inverted: function(p) {
          return 1 - p;
        }
      },
      smoothScrolling: true,
      forceHeight: false
    });
  }
});


$(window).load(function() {
  'use strict';
  //preloader
  $(window).scrollTop(0);

  //modal trigger
  //$('.modal').bigmodal('hide');
  $.fn.modal.Constructor.prototype.enforceFocus = function() {};

  //tooltip and popover trigger
  $('[data-thumb=tooltip]').tooltip();
  $('[data-thumb=popover]').popover();

});