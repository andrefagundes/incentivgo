$(document).ready(function() {
	function addNotification(i){
		var $el     = $('#panel-notifications');
            $('.count-n', $el).fadeOut().fadeIn().text(i+1);
	}

    $.post("mensagem/verificar-mensagens", null, function(data) {
        $.each(data, function(i){
            setTimeout(addNotification(i),'2000');
        });
    }, 'json');
});