var ModalAjudar = {

    init: function() {
        $("[data-toggle=tooltip]").tooltip();
        $("#btnAjudaEnvio,#btnCancelarAjuda").off();
        $("#btnPedirAjuda").hide();
        $("#modal-footer-ajuda").show();
        
        $("form#form_ajudar").validate({
            rules: {
                'txt_mensagem_ajudar': {
                    required: true
                }
            },
            messages: {
                'txt_mensagem_ajudar': 'Campo obrigatório'
            }
        });
        
        if($("#mensagem-modal-resposta-ajudar")){
            $("#mensagem-modal-resposta-ajudar").slideUp({start:3000,duration: 5000});
        }
        moment.locale('pt-br');
   
        $(".moment").each(function(){
            $(this).html("Enviada "+ moment($(this).html()).startOf().fromNow());
        });
        
        $("#btnAjudaEnvio").click(ModalAjudar.ajudar);
        $("#btnCancelarAjuda").click(ModalAjudar.cancelarAjuda);   
    },
    
    ajudar:function(){
        if($("#form_ajudar").valid()){
            var ajuda = $("#txt_mensagem_ajudar").val();
            var ajudaId = $("#ajuda-id").val();
            $.post("ajudar", {'ajuda':ajuda,'ajudaId':ajudaId }, function() {
                $("#modal-body-ajudas").load( "modal-ajudar/"+ajudaId );
            }, 'json');
        }
    },
    cancelarAjuda: function(){
        $("#modal-body-ajudas").html('').load( "modal-ajudas/"+0 );
    }
}; 