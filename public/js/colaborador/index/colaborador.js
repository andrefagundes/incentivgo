var Colaborador = {
    init: function() {
        //evento do botão encarar desafios
        $("#btnEncarar").click(function() {
            $("#myModalLabelDesafios").html('Desafios');
            $("#modal-body-desafios").html('').load("modal-desafios/" + 0);
            $("#modalDesafios").modal('show');
        });

        //evento do botão pedir ajuda
        $("#btnAjuda").click(function() {
            $("#myModalLabelAjudas").html('Ajudas');
            $("#modal-body-ajudas").html('').load("modal-ajudas/" + 0);
            $("#modalAjudas").modal('show');
        });
        //evento do botão noticias
        $("#btnVerNoticias").click(function() {
            $("#myModalLabelNoticias").html('Notícias');
            $("#modal-body-noticias").html('').load("modal-noticias/" + 0);
            $("#modalNoticias").modal('show');
        });
        //evento do botão anotacoes
        $("#btnInserirAnotacoes").click(function() {
            $("#myModalLabelAnotacoes").html('Anotações');
            $("#modal-body-anotacoes").html('').load("modal-anotacoes/" + 0);
            $("#modalAnotacoes").modal('show');
        });
    }
};