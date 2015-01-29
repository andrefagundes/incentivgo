var pesquisarNoticia = {
    init: function(){
        $("[data-toggle=tooltip]").tooltip();
        
        $('.btnPaginacaoNoticia').on('click', function() {
            Noticia.pesquisarNoticia(this.id);
        }); 
       
        $('.btnAlterarNoticia').click(function() {
            $("#myModalLabel").html('Alterar Notícia');
            $( "#modal-body-noticia" ).html('').load( "noticia/modal-noticia/"+this.id );
            $('#modalCriarNoticia').modal('show'); 
        });

    }
};


