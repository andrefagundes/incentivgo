var pesquisarPedidos = {
    init: function(){
        $("[data-toggle=tooltip]").tooltip();
        
        $('.btnPaginacaoPedido').on('click', function() {
            Pedido.pesquisarPedido(this.id);
        }); 

    }
};


