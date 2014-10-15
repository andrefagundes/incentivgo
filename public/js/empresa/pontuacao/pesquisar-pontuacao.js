var pesquisarPontuacao = {
    init: function(){
        $("[data-toggle=tooltip]").tooltip();
        $('.btnPaginacaoRegraPontuacao').on('click', function() {
            Pontuacao.pesquisarPontuacao(this.id);
        }); 
       
        $('.btnAlterarRegraPontuacao').click(function() {
            $("#myModalLabel").html('Criar Regra Pontuação');
            $( "#modal-body-pontuacao" ).html('').load( "pontuacao/modal-pontuacao/"+this.id );
            $('#modalCriarRegraPontuacao').modal('show'); 
        });
    }         
};


