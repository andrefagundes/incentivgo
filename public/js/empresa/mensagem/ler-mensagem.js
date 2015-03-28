var LerMensagem = {
    init: function() {
        moment.locale('pt-br');
        $(".moment").each(function(){
           $(this).html(moment($(this).html()).startOf().fromNow());
        });
        
        $(".btnResponderMensagem").click();
        $("#btnExcluirMensagemLida").click(LerMensagem.excluirMensagem);
        
        $("#btnExcluirMensagem").click(function(){
            $("#myModalLabel").html('Confirmação');
            $('#modalExcluirMsg').modal('show'); 
        });
    },
    excluirMensagem:function(){
        $('#modalExcluirMsg').modal('hide'); 
        var id = $("#id").val();
        
        $.post( "mensagem/excluir-mensagem", { 'id':id }, function(){
             Mensagem.pesquisarMensagem(1);
        },'json');
    }
};