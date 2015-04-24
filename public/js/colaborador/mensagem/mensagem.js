var Mensagem = {
    init: function(){  
        $("[data-toggle=tooltip]").tooltip();
        //marcar menu ativo
        $(".dropdown-submenu").removeClass('active');
        $("#menu-mensagem").addClass('active');
        
        if($("div.alert")){
            $("div.alert").slideUp({start:3000,duration: 5000});
        }
        
        $("#btnMensagensEntrada").click(function(){
            $("#filtro-mensagem").val('');
            Mensagem.removerClassActive();
            $(this).addClass('active');
            $("#tipo-mensagem").val(1);
            Mensagem.pesquisarMensagem(1);
        });
        $("#btnMensagensEnviadas").click(function(){
            $("#filtro-mensagem").val('');
            Mensagem.removerClassActive();
            $(this).addClass('active');
            $("#tipo-mensagem").val(2);
            Mensagem.pesquisarMensagem(1);
        });
        $("#btnMensagensExcluidas").click(function(){
            $("#filtro-mensagem").val('');
            Mensagem.removerClassActive();
            $(this).addClass('active');
            $("#tipo-mensagem").val(3);
            Mensagem.pesquisarMensagem(1);
        });
        $("#btnFiltroTodas").click(function(){
            $("#filtro-mensagem").val('');
            Mensagem.pesquisarMensagem(1);
        });
        $("#btnFiltroLidas").click(function(){
            $("#filtro-mensagem").val('Y');
            Mensagem.pesquisarMensagem(1);
        });
        $("#btnFiltroNaoLidas").click(function(){
            $("#filtro-mensagem").val('N');
            Mensagem.pesquisarMensagem(1);
        });
        
        $("#btnAtualizarPesquisa").click(function(){
            Mensagem.pesquisarMensagem(1);
        });

        $("#btnMensagensEntrada").addClass('active');
        Mensagem.pesquisarMensagem(1);
    },
    removerClassActive: function(){
        $("[id^='btnMensagens']").removeClass('active');
    },
    pesquisarMensagem: function(page){

        var tipo   = $("#tipo-mensagem").val();
        var filter  = $("#filtro-mensagem").val();
        $("#grupo-filtros").show();
        $.post( "mensagem/pesquisar-mensagem", { 'page': page, 'tipo':tipo, 'filter':filter }, function(data){
             $( "#lista-mensagens" ).empty().append( data );
        },'html');
    },
    novaMensagem:function(){
        $("#grupo-filtros").hide();
        $( "#lista-mensagens" ).empty().load('mensagem/nova-mensagem');
    },
    lerMensagem:function(id){
        $("#grupo-filtros").hide();
        $( "#lista-mensagens" ).empty().load('mensagem/ler-mensagem/'+id);
    }
};