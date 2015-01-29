var ModalNoticias = {
    init: function() {
        $(".btnLerNoticia").click(ModalNoticias.lerNoticia);
    },
    lerNoticia: function() {
        var noticiaId = $(this).attr('id');
        $("#modal-body-noticias").html('').load("noticia/modal-ler-noticia/" + noticiaId);
    }
}; 