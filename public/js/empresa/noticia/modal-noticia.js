var ModalNoticia = {
    init: function() {

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
                'dados[titulo]': 'Campo obrigatório',
                'dados[noticia]': 'Campo obrigatório'
            }
        });
    }
};