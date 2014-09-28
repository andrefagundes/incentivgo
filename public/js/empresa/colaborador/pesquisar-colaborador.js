var pesquisarColaborador = {
    init: function(){
        // tooltip
        $("[data-toggle=tooltip]").tooltip();
       $('.btnPaginacaoColaborador').on('click', function() {
           Colaborador.pesquisarColaborador(this.id);
       }); 
    }
};


