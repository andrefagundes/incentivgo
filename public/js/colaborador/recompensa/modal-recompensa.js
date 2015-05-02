var ModalRecompensa = {
    
    init: function() {
        
         $("#form-recompensa").validate({
            rules: {
                'dados[recompensa_id]': {
                    required: true
                }
            },
            messages: {
                'dados[recompensa_id]': 'Campo obrigatório'
            }
        });
        
        if($("#mensagem-modal-resposta-recompensa")){
            $("#mensagem-modal-resposta-recompensas").slideUp({start:3000,duration: 6000});
        }
        
        $("#btnEnviarRecompensa").click(ModalRecompensa.recompensa);
        $(".btnExcluirRecompensa").click(ModalRecompensa.excluirRecompensa);
        $("#btnSalvarRecompensa").click(ModalRecompensa.salvarRecompensa);
        $("#btnCancelarRecompensa").click(ModalRecompensa.cancelarRecompensa);   
    },
    recompensa:function(){
        $(".ul-recompensas").hide();
        $("#ul_recompensa").show();
    },
    salvarRecompensa:function(){
        if($("#form-recompensa").valid()){
            var recompensaId        = $("#recompensa_id").val();
            var observacaoUsuario   = $("#observacaoUsuario").val();
            $.post("recompensa/salvar-recompensa", { 'recompensaId':recompensaId,'observacaoUsuario':observacaoUsuario }, function() {
                $("#modal-body-recompensas").html('').load( "recompensa/modal-recompensa/"+0 );
            }, 'json');
        }
    },
    excluirRecompensa:function(){            
        var recompensaId = $(this).parent().attr('id');

        $.post("recompensa/excluir-recompensa", {'recompensaId':recompensaId }, function() {
            $("#modal-body-recompensas").html('').load( "recompensa/modal-recompensa/"+0 );
        }, 'json');
    },
    cancelarRecompensa: function(){
        $("#modal-body-recompensas").html('').load( "recompensa/modal-recompensa/"+0 );
    }
}; 

