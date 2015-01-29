var Noticia = {
    init: function(){
        
        //evento do botão de pesquisar noticia
        $("#btnPesquisarNoticia").click(function(){
            Noticia.pesquisarNoticia(1);
        });
        
        if($("#mensagem-modal-noticia")){
            $("#mensagem-modal-noticia").slideUp({start:3000,duration: 5000});
        }
        
        //evento do botão de criar noticia
        $("#btnCriarNoticia").click(function() {
            $("#myModalLabel").html('Criar Notícia');
            $( "#modal-body-noticia" ).html('').load( "noticia/modal-noticia/"+0 );
            $('#modalCriarNoticia').modal('show'); 
        });

        Noticia.pesquisarNoticia();
    },
    pesquisarNoticia: function(page){
        var ativo   = 'T';
        var filter  = $("#filterNoticia").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });
        
        $.post( "noticia/pesquisar-noticia", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarNoticia" ).empty().append( data );
             Noticia.inserirQuantidadeNoticia($("#quantNoticia").val());
        },'html');
    },
    inserirQuantidadeNoticia:function(quantNoticia){
       $("#quantidade-noticia").html('').html(quantNoticia);
    }
};