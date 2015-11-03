var pesquisarIdeia = {
    init: function(lang) {
        pesquisarIdeia.lang = lang;
        $("[data-toggle=tooltip]").tooltip();
        $('.btnPaginacaoIdeia').on('click', function() {
            Ideia.pesquisarIdeia(this.id);
        });
        //evento do botão de criar desafio
        $(".btnAnalisarIdeia").click(function() {
            $("#myModalLabel").html((pesquisarIdeia.lang === 'en' ? 'Analyze Idea' : 'Analisar Ideia'));
            $("#modal-body-ideia").html('').load("ideia/modal-ideia/" + $(this).attr('id'));
            $("#modalAnalisarIdeia").modal('show');
        });
    }
};