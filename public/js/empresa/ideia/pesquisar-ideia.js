var pesquisarIdeia = {
    init: function() {
        $("[data-toggle=tooltip]").tooltip();
        $('.btnPaginacaoIdeia').on('click', function() {
            Ideia.pesquisarIdeia(this.id);
        });
        //evento do botão de criar desafio
        $(".btnAnalisarIdeia").click(function() {
            $("#myModalLabel").html('Analisar Ideia');
            $("#modal-body-ideia").html('').load("ideia/modal-ideia/" + $(this).attr('id'));
            $("#modalAnalisarIdeia").modal('show');
        });
    }
};