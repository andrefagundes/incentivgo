var LerMensagem = {
    init: function(lang) {
        LerMensagem.lang = lang;
        $("[data-toggle=tooltip]").tooltip();
        
        $(".moment").each(function(){
           $(this).html(moment($(this).html()).locale(LerMensagem.lang).startOf().fromNow());
        });
        
        $("#btnResponderMensagem").click(LerMensagem.responderMensagem);
        $("#btnExcluirMensagemLida").click(LerMensagem.excluirMensagem);
        
        $(".link-responder-mensagem").click(function(){
            $("#div-resposta-mensagem").show();
            $("#resposta-titulo").focus();
        });
        
        $("#btnCancelarResposta").click(function(){
            $("#div-resposta-mensagem").hide();
        });
        
        $("#btnExcluirMensagem").click(function(){
            $("#myModalLabel").html((LerMensagem.lang === 'pt-BR' ? 'Confirmação' : 'Confirmation'));
            $('#modalExcluirMsg').modal('show'); 
        });
        
        $("#form-responder-mensagem").validate({
            rules: {
                'resposta-titulo': {
                    required: true
                },
                'resposta-mensagem': {
                    required: true
                }
            },
            messages: {
                'resposta-titulo':(LerMensagem.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                'resposta-mensagem':(LerMensagem.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required')
            }
        });
    },
    excluirMensagem:function(){
        $('#modalExcluirMsg').modal('hide'); 
        var id = $("#id").val();
        $.post( "mensagem/excluir-mensagem", { 'id':id }, function(data){
             location.reload();
        },'json');
    },
    responderMensagem:function(){
         var id = $("#id").val();
        
        $.post( "mensagem/responder-mensagem", { 'id':id }, function(){
             ColaboradorMensagem.pesquisarMensagem(1);
        },'json');
    }
    
};