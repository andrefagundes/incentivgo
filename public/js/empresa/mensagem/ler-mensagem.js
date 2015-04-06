var LerMensagem = {
    init: function() {
        moment.locale('pt-br');
        $(".moment").each(function(){
           $(this).html(moment($(this).html()).startOf().fromNow());
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
            $("#myModalLabel").html('Confirmação');
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
                'resposta-titulo':'Título obrigatório',
                'resposta-mensagem':'Resposta obrigatória'
            }
        });
    },
    excluirMensagem:function(){
        $('#modalExcluirMsg').modal('hide'); 
        var id = $("#id").val();
        
        $.post( "mensagem/excluir-mensagem", { 'id':id }, function(){
             Mensagem.pesquisarMensagem(1);
        },'json');
    },
    responderMensagem:function(){
         var id = $("#id").val();
         var id = $("#id").val();
        
        $.post( "mensagem/responder-mensagem", { 'id':id }, function(){
             Mensagem.pesquisarMensagem(1);
        },'json');
    }
    
};