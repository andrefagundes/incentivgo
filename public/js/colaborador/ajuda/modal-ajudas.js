var ModalAjudas = {
    
    init: function() {
        
        $("#btnPedirAjuda").show();
        $("#modal-footer-ajuda").hide();
        
        $("#form_ajuda").validate({
            rules: {
                'txt_mensagem_ajuda': {
                    required: true
                }
            },
            messages: {
                'txt_mensagem_ajuda': 'Campo obrigatório'
            }
        });
        
        if($("#mensagem-modal-resposta-ajuda")){
            $("#mensagem-modal-resposta-ajudas").slideUp({start:3000,duration: 5000});
        }
        moment.locale('pt-br');
   
        $(".moment").each(function(){
            $(this).html("Enviada "+ moment($(this).html()).startOf().fromNow());
        });
        
        $(".btnAjudar").click(ModalAjudas.ajudar);
        $("#btnPedirAjuda").click(ModalAjudas.pedirAjuda);
        $(".btnExcluirAjuda").click(ModalAjudas.excluirAjuda);
        $("#btnEnviarAjuda").click(ModalAjudas.enviarAjuda);
        $("#btnCancelarAjuda").click(ModalAjudas.cancelarAjuda);   
    },
    ajudar:function(){
        var ajudaId = $(this).parent().attr('id');
        $("#modal-body-ajudas").html('').load( "colaborador/modal-ajudar/"+ajudaId );
    },
    pedirAjuda:function(){
        $(".ul-ajudas").hide();
        $("#ul_ajuda").show();
    },
    enviarAjuda:function(){
        if($("#form_ajuda").valid()){
            var ajuda = $("#txt_mensagem_ajuda").val();
            $.post("colaborador/ajuda/pedir-ajuda", {'ajuda':ajuda }, function() {
                $("#modal-body-ajudas").html('').load( "colaborador/modal-ajudas/"+0 );
            }, 'json');
        }
    },
    excluirAjuda:function(){            
        var ajudaId = $(this).parent().attr('id');

        $.post("colaborador/ajuda/excluir-ajuda", {'ajudaId':ajudaId }, function() {
            $("#modal-body-ajudas").html('').load( "colaborador/modal-ajudas/"+0 );
        }, 'json');
    },
    cancelarAjuda: function(){
        $("#modal-body-ajudas").html('').load( "colaborador/modal-ajudas/"+0 );
    }
}; 