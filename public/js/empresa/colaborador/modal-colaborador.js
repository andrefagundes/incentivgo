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
                'nome': (ModalColaborador.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                'email':{required:(ModalColaborador.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),email:(ModalColaborador.lang === 'pt-BR' ? 'E-mail inválido' : 'Invalid email')},
                'perfilId': (ModalColaborador.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required')
            }
        });
    }
};