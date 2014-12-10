var pesquisarIdeia = {
    init: function(){
        $("[data-toggle=tooltip]").tooltip();
        $('.btnPaginacaoIdeia').on('click', function() {
           Ideia.pesquisarIdeia(this.id);
        }); 
        $('.btnAlterarIdeia').click(function() {
            $("#myModalLabel").html('Alterar Usuário');
            $( "#modal-body-ideia" ).html('').load( "ideia/modal-ideia/"+this.id );
            $('#modalCriarIdeia').modal('show'); 
        });
    }
};


