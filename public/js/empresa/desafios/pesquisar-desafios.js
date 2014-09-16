var pesquisarDesafios = {
    init: function(){
        $('.btnPaginacaoDesafio').on('click', function() {
            Desafios.pesquisarDesafios(this.id);
        }); 
       
        $('.btnAlterar').click(function() {
            $("#myModalLabel").html('Criar Desafio');
            $( "#modal-body-desafio" ).html('').load( "modal-desafio/"+this.id );
            $('#modalCriarDesafio').modal('show'); 
        });
    }         
};


