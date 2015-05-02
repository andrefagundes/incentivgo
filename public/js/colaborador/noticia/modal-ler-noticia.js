var ModalLerNoticia = {
    init: function() {
        $("#btnVoltarNoticia").click(ModalLerNoticia.voltarNoticia);
    },
    voltarNoticia: function() {
        $("#modal-body-noticias").html('').load("modal-noticias/" + 0);
    }
}; 