var pesquisarMensagem = {
    init: function() {
        $("[data-toggle=tooltip]").tooltip();
        
        moment.locale('pt-br');
        $(".moment").each(function(){
           $(this).html(moment($(this).html()).startOf().fromNow());
        });
        
        $('.btnPaginacaoMensagem').on('click', function() {
            ColaboradorMensagem.pesquisarMensagem(this.id);
        });
        
        $(".mensagem-entrada,#btnNovaMensagem").off();
        $(".mensagem-entrada").click(pesquisarMensagem.lerMensagem);
        $("#btnNovaMensagem").click(ColaboradorMensagem.novaMensagem);
    },
    lerMensagem:function(){
        ColaboradorMensagem.lerMensagem(this.id);
    }
};