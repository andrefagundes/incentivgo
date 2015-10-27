$(document).ready(function() {

	 $('#docs pre code').each(function(){
	    var $this = $(this);
	    var t = $this.html();
	    $this.html(t.replace(/</g, '&lt;').replace(/>/g, '&gt;'));
	});

	function getRandomInt(min, max) {
	  return Math.floor(Math.random() * (max - min + 1)) + min;
	};

	$(document).on('click', '.the-icons a', function(e){
		e && e.preventDefault();
	});

	$(document).on('change', 'table thead [type="checkbox"]', function(e){
		e && e.preventDefault();
		var $table = $(e.target).closest('table'), $checked = $(e.target).is(':checked');
		$('tbody [type="checkbox"]', $table).prop('checked', $checked);
	});

	$(document).on('click', '[data-toggle^="progress"]', function(e){
		e && e.preventDefault();

		$el = $(e.target);
		$target = $($el.data('target'));
		$('.progress', $target).each(
			function(){
				var $max = 50, $data, $ps = $('.progress-bar',this).last();
				($(this).hasClass('progress-mini') || $(this).hasClass('progress-small')) && ($max = 100);
				$data = Math.floor(Math.random()*$max)+'%';
				$ps.css('width', $data).attr('data-original-title', $data);
			}
		);
	});
	
	function addNotification($notes){
            
		var $el     = $('#panel-notifications'), 
                    $n      = $('.count-n:first', $el), 
                    $item   = $('.list-group-item:first', $el).clone(), 
                    $v      = parseInt($n.text());
   	
            $('.count-n', $el).fadeOut().fadeIn().text($v+1);
		$item.attr('href', $notes.link);
		$item.find('.pull-left').html($notes.icon);
		$item.find('.media-body').html($notes.title);
		$item.hide().prependTo($el.find('.list-group')).slideDown().css('display','block');
	}
//	var $noteMail = {
//		icon: '<i class="fa fa-envelope-o fa-2x text-default"></i>',
//		title: 'Add the mail app, Check it out.<br><small class="text-muted">2 July 13</small>',
//		link: 'mail.html'
//	}
//	var $noteCalendar = {
//		icon: '<i class="fa fa-calendar fa-2x text-default"></i>',
//		title: 'Added the calendar, Get it.<br><small class="text-muted">10 July 13</small>',
//		link: 'calendar.html'
//	}
//	var $noteTimeline = {
//		icon: '<i class="fa fa-clock-o fa-2x text-default"></i>',
//		title: 'Added the timeline, view it here.<br><small class="text-muted">1 minute ago</small>',
//		link: 'timeline.html'
//	}

   
//	window.setTimeout(function(){addNotification($noteMail)}, 2000);
//	window.setTimeout(function(){addNotification($noteCalendar)}, 3500);
//	window.setTimeout(function(){addNotification($noteTimeline)}, 5000);
    
    $.post("mensagem/verificar-mensagens", null, function(data) {
            $.each(data, function(i,item){
                var mensagem = {
                icon: '<i class="fa fa-clock-o fa-2x text-default"></i>',
                title: item.titulo+'<br><small class="text-muted">'+item.envioDt+'</small>',
                link: 'mensagens'
            };
            addNotification(mensagem);
        });

    }, 'json');

	$('#myEvents').on('change', function(e, item){
		addDragEvent($(item));
	});

	$('#myEvents li').each(function() {
		addDragEvent($(this));
	});

	// select2 

   if ($.fn.select2) {
        $("#select2-option").select2();
    }


});