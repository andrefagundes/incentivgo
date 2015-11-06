var pesquisarMensagem = {
    init: function(lang) {
        pesquisarMensagem.lang = lang;
        $("[data-toggle=tooltip]").tooltip();
        
        $(".moment").each(function(){
           $(this).html(moment($(this).html()).locale(pesquisarMensagem.lang).startOf().fromNow());
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