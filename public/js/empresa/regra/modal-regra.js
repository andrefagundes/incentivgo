var ModalRegra = {
    init: function() {
        $("#form-regra").validate({
            rules: {
                'dados[regra]': {
                    required: true
                },
                'dados[pontuacao]': {
                    required: true
                }
            },
            messages: {
                'dados[regra]': 'Campo obrigatório',
                'dados[pontuacao]': 'Campo obrigatório'
            }
        });
    }
};