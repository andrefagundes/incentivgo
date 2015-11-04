var Colaborador = {
    init: function(lang) {
        Colaborador.lang = lang;
         //marcar menu ativo
        $(".submenu a").removeClass('active');
        $("#menu-geral-colaborador a").addClass('active');
        
        //evento do botão encarar desafios
        $("#btnEncarar").click(function() {
            $("#myModalLabelDesafios").html((Colaborador.lang === 'pt-BR' ? 'Desafios' : 'Challenges'));
            $("#modal-body-desafios").html('').load("modal-desafios/" + 0);
            $("#modalDesafios").modal('show');
        });

        //evento do botão pedir ajuda
        $("#btnAjuda").click(function() {
            $("#myModalLabelAjudas").html((Colaborador.lang === 'pt-BR' ? 'Ajudas' : 'Help'));
            $("#modal-body-ajudas").html('').load("modal-ajudas/" + 0);
            $("#modalAjudas").modal('show');
        });
        //evento do botão noticias
        $("#btnVerNoticias").click(function() {
            $("#myModalLabelNoticias").html((Colaborador.lang === 'pt-BR' ? 'Notícias' : 'News'));
            $("#modal-body-noticias").html('').load("modal-noticias/" + 0);
            $("#modalNoticias").modal('show');
        });
        //evento do botão anotacoes
        $("#btnInserirAnotacoes").click(function() {
            $("#myModalLabelAnotacoes").html((Colaborador.lang === 'pt-BR' ? 'Anotações' : 'Notes'));
            $("#modal-body-anotacoes").html('').load("modal-anotacoes/" + 0);
            $("#modalAnotacoes").modal('show');
        });
    }
};