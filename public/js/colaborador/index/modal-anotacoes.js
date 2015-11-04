var ModalAnotacoes = {
    
    init: function(lang) {
        ModalAnotacoes.lang = lang;
        $("#form-anotacao").validate({
            rules: {
                'txt_anotacao': {
                    required: true
                }
            },
            messages: {
                'txt_anotacao': (ModalAnotacoes.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required')
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
            $.post("anotacao/salvar-anotacao", {'descricao':anotacao }, function() {
                $("#modal-body-anotacoes").html('').load( "modal-anotacoes/"+0 );
            }, 'json');
        }
    },
    excluirAnotacao:function(){            
        var anotacaoId = $(this).parent().attr('id');

        $.post("anotacao/excluir-anotacao", {'anotacaoId':anotacaoId }, function() {
            $("#modal-body-anotacoes").html('').load( "modal-anotacoes/"+0 );
        }, 'json');
    },
    cancelarAnotacao: function(){
        $("#modal-body-anotacoes").html('').load( "modal-anotacoes/"+0 );
    }
}; 