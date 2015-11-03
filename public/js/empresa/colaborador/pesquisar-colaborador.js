var pesquisarColaborador = {
    init: function(lang){
        pesquisarColaborador.lang = lang;
        $("[data-toggle=tooltip]").tooltip();
        $('.btnPaginacaoColaborador').on('click', function() {
           Colaborador.pesquisarColaborador(this.id);
        }); 
        $('.btnAlterarColaborador').click(function() {
            $("#myModalLabel").html((pesquisarColaborador.lang === 'en' ? 'Change User' : 'Alterar Usuário'));
            $( "#modal-body-colaborador" ).html('').load( "colaborador/modal-colaborador/"+this.id );
            $('#modalCriarColaborador').modal('show'); 
        });
    }
};


