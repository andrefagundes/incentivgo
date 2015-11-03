var ModalNoticia = {
    init: function(lang) {
        ModalNoticia.lang = lang;
        $("#form-noticia").validate({
            rules: {
                'dados[titulo]': {
                    required: true
                },
                'dados[noticia]': {
                    required: true
                }
            },
            messages: {
                'dados[titulo]': (ModalNoticia.lang === 'en' ? 'Required' : 'Campo obrigatório'),
                'dados[noticia]': (ModalNoticia.lang === 'en' ? 'Required' : 'Campo obrigatório')
            }
        });
    }
};