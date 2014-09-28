var ModalColaborador = {
    init: function() {
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
                'nome': 'Campo obrigatório',
                'email':{required:'Campo obrigatório',email:'E-mail inválido'},
                'perfilId': 'Campo obrigatório'
            }
        });
    }
};