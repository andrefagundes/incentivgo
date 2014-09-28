var pesquisarRegra = {
    init: function(){
        $('.btnPaginacaoRegra').on('click', function() {
            Regra.pesquisarRegra(this.id);
        }); 
       
        $('.btnAlterarRegra').click(function() {
            $("#myModalLabel").html('Criar Regra');
            $( "#modal-body-regra" ).html('').load( "regra/modal-regra/"+this.id );
            $('#modalCriarRegra').modal('show'); 
        });
    }         
};


