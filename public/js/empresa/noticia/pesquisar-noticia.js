var pesquisarNoticia = {
    init: function(lang){
        pesquisarNoticia.lang = lang;
        $("[data-toggle=tooltip]").tooltip();
        
        $('.btnPaginacaoNoticia').on('click', function() {
            Noticia.pesquisarNoticia(this.id);
        }); 
       
        $('.btnAlterarNoticia').click(function() {
            $("#myModalLabel").html((pesquisarNoticia.lang === 'en' ? 'Create News' : 'Alterar Notícia'));
            $( "#modal-body-noticia" ).html('').load( "noticia/modal-noticia/"+this.id );
            $('#modalCriarNoticia').modal('show'); 
        });

    }
};


