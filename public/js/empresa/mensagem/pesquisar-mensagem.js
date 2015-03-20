var pesquisarMensagem = {
    init: function() {
        $("[data-toggle=tooltip]").tooltip();
        $('.btnPaginacaoMensagem').on('click', function() {
            Mensagem.pesquisarMensagem(this.id);
        });
        
        $("#btnNovaMensagem").click(Mensagem.novaMensagem);
    }
};