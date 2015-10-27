var pesquisarDesafio = {
    init: function(lang){
        pesquisarDesafio.lang = lang;
        $("[data-toggle=tooltip]").tooltip();
        
        $('.btnPaginacaoDesafio').on('click', function() {
            Desafio.pesquisarDesafio(this.id);
        }); 
       
        $('.btnAlterarDesafio').click(function() {
            $("#myModalLabel").html((pesquisarDesafio.lang === 'en' ? 'Change Challenge' : 'Alterar Desafio'));
            $( "#modal-body-desafio" ).html('').load( "desafio/modal-desafio/"+this.id );
            $('#modalCriarDesafio').modal('show'); 
        });

    }
};


