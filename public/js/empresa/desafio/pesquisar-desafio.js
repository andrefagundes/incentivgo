var pesquisarDesafio = {
    init: function(){
        $("[data-toggle=tooltip]").tooltip();
        
        $('.btnPaginacaoDesafio').on('click', function() {
            Desafio.pesquisarDesafio(this.id);
        }); 
       
        $('.btnAlterarDesafio').click(function() {
            $("#myModalLabel").html('Alterar Desafio');
            $( "#modal-body-desafio" ).html('').load( "desafio/modal-desafio/"+this.id );
            $('#modalCriarDesafio').modal('show'); 
        });

    }
};


