var pesquisarRecompensa = {
    init: function(lang){
        pesquisarRecompensa.lang = lang;
        $("[data-toggle=tooltip]").tooltip();
        $('.btnPaginacaoRecompensa').on('click', function() {
            Recompensa.pesquisarRecompensa(this.id);
        }); 
       
        $('.btnAlterarRecompensa').click(function() {
            $("#myModalLabel").html((pesquisarRecompensa.lang === 'en' ? 'Create Reward' : 'Criar Recompensa'));
            $( "#modal-body-recompensa" ).html('').load( "recompensa/modal-recompensa/"+this.id );
            $('#modalCriarRecompensa').modal('show'); 
        });
    }         
};


