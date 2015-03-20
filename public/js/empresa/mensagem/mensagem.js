var Mensagem = {
    init: function(){     
        if($("div.alert")){
            $("div.alert").slideUp({start:3000,duration: 5000});
        }
        
        Mensagem.pesquisarMensagem(1);
    },
    pesquisarMensagem: function(page){

        //T de todas as ideias
        var ativo   = 'T';
        var filter  = $("#filterMensagem").val();
       
        $.post( "mensagem/pesquisar-mensagem", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#lista-mensagens" ).empty().append( data );
        },'html');
    },
    novaMensagem:function(){
        $("#grupo-filtros").hide();
        $( "#lista-mensagens" ).empty().load('mensagem/nova-mensagem');
    }
};