var ModalAnotacoes = {
    
    init: function() {
        
        $("#form-anotacao").validate({
            rules: {
                'txt_anotacao': {
                    required: true
                }
            },
            messages: {
                'txt_anotacao': 'Campo obrigatório'
            }
        });
        
        if($("#mensagem-modal-resposta-anotacao")){
            $("#mensagem-modal-resposta-anotacoes").slideUp({start:3000,duration: 5000});
        }
        
        $("#btnAnotar").click(ModalAnotacoes.anotar);
        $(".btnExcluirAnotacao").click(ModalAnotacoes.excluirAnotacao);
        $("#btnSalvarAnotacao").click(ModalAnotacoes.salvarAnotacao);
        $("#btnCancelarAnotacao").click(ModalAnotacoes.cancelarAnotacao);   
    },
    anotar:function(){
        $(".ul-anotacoes").hide();
        $("#ul_anotacao").show();
    },
    salvarAnotacao:function(){
        if($("#form-anotacao").valid()){
            var anotacao = $("#txt_anotacao").val();
            $.post("colaborador/anotacao/salvar-anotacao", {'descricao':anotacao }, function() {
                $("#modal-body-anotacoes").html('').load( "colaborador/modal-anotacoes/"+0 );
            }, 'json');
        }
    },
    excluirAnotacao:function(){            
        var anotacaoId = $(this).parent().attr('id');

        $.post("colaborador/anotacao/excluir-anotacao", {'anotacaoId':anotacaoId }, function() {
            $("#modal-body-anotacoes").html('').load( "colaborador/modal-anotacoes/"+0 );
        }, 'json');
    },
    cancelarAnotacao: function(){
        $("#modal-body-anotacoes").html('').load( "colaborador/modal-anotacoes/"+0 );
    }
}; 