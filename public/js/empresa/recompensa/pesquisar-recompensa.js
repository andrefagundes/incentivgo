var pesquisarRecompensa = {
    init: function(){
        $("[data-toggle=tooltip]").tooltip();
        $('.btnPaginacaoRecompensa').on('click', function() {
            Recompensa.pesquisarRecompensa(this.id);
        }); 
       
        $('.btnAlterarRecompensa').click(function() {
            $("#myModalLabel").html('Criar Recompensa');
            $( "#modal-body-recompensa" ).html('').load( "recompensa/modal-recompensa/"+this.id );
            $('#modalCriarRecompensa').modal('show'); 
        });
    }         
};


