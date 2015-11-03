var ModalColaborador = {
    init: function(lang) {
        ModalColaborador.lang = lang;
        $("#form-colaborador").validate({
            rules: {
                'nome': {
                    required: true
                },
                'email': {
                    required: true,
                    email:true
                },
                'perfilId': {
                    required: true
                }
            },
            messages: {
                'nome': (ModalColaborador.lang === 'en' ? 'Required' : 'Campo obrigatório'),
                'email':{required:(ModalColaborador.lang === 'en' ? 'Required' : 'Campo obrigatório'),email:(ModalColaborador.lang === 'en' ? 'Invalid email' : 'E-mail inválido')},
                'perfilId': (ModalColaborador.lang === 'en' ? 'Required' : 'Campo obrigatório'),
            }
        });
    }
};