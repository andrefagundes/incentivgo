var pesquisarColaborador = {
    init: function(){
        $("[data-toggle=tooltip]").tooltip();
        $('.btnPaginacaoColaborador').on('click', function() {
           Colaborador.pesquisarColaborador(this.id);
        }); 
        $('.btnAlterarColaborador').click(function() {
            $("#myModalLabel").html('Alterar Usuário');
            $( "#modal-body-colaborador" ).html('').load( "colaborador/modal-colaborador/"+this.id );
            $('#modalCriarColaborador').modal('show'); 
        });
    }
};


