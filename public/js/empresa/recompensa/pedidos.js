var Pedido = {
    init: function(){
        
        //marcar menu ativo
        $(".dropdown-submenu").removeClass('active');
        $("#menu-recompensa").addClass('active');
        
        //evento do botão de pesquisar pedido
        $("#btnPesquisarPedido").click(function(){
            Pedido.pesquisarPedido(1);
        });
        
        if($("div.alert")){
            $("div.alert").slideUp({start:3000,duration: 5000});
        }

        Pedido.pesquisarPedido();
    },
    pesquisarPedido: function(page){
        var ativo   = 'T';
        var filter  = $("#filterPedido").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });
        
        $.post( "pesquisar-pedidos", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarPedido" ).empty().append( data );
        },'html');
    }
};