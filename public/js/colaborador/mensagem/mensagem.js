var ColaboradorMensagem = {
    init: function(){  
        
        $("[data-toggle=tooltip]").tooltip();
        //marcar menu ativo
        $("[id^='menu-'] a").removeClass('active');
        $("#menu-mensagem-colaborador a").addClass('active');
        
        if($("div.alert")){
            $("div.alert").slideUp({start:3000,duration: 5000});
        }
        
        $("#btnMensagensEntrada").click(function(){
            $("#filtro-mensagem").val('');
            ColaboradorMensagem.removerClassActive();
            $(this).addClass('active');
            $("#tipo-mensagem").val(1);
            ColaboradorMensagem.pesquisarMensagem(1);
        });
        $("#btnMensagensEnviadas").click(function(){
            $("#filtro-mensagem").val('');
            ColaboradorMensagem.removerClassActive();
            $(this).addClass('active');
            $("#tipo-mensagem").val(2);
            ColaboradorMensagem.pesquisarMensagem(1);
        });
        $("#btnMensagensExcluidas").click(function(){
            $("#filtro-mensagem").val('');
            ColaboradorMensagem.removerClassActive();
            $(this).addClass('active');
            $("#tipo-mensagem").val(3);
            ColaboradorMensagem.pesquisarMensagem(1);
        });
        $("#btnFiltroTodas").click(function(){
            $("#filtro-mensagem").val('');
            ColaboradorMensagem.pesquisarMensagem(1);
        });
        $("#btnFiltroLidas").click(function(){
            $("#filtro-mensagem").val('Y');
            ColaboradorMensagem.pesquisarMensagem(1);
        });
        $("#btnFiltroNaoLidas").click(function(){
            $("#filtro-mensagem").val('N');
            ColaboradorMensagem.pesquisarMensagem(1);
        });
        
        $("#btnAtualizarPesquisa").click(function(){
            ColaboradorMensagem.pesquisarMensagem(1);
        });

        $("#btnMensagensEntrada").addClass('active');
        ColaboradorMensagem.pesquisarMensagem(1);
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