var pesquisarDesafio = {
    init: function(){
        $('.btnPaginacaoDesafio').on('click', function() {
            Desafio.pesquisarDesafio(this.id);
        }); 
       
        $('.btnAlterar').click(function() {
            $("#myModalLabel").html('Criar Desafio');
            $( "#modal-body-desafio" ).html('').load( "desafio/modal-desafio/"+this.id );
            $('#modalCriarDesafio').modal('show'); 
        });
    }         
};


