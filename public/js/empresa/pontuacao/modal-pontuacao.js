var ModalPontuacao = {
    init: function() {
        $("#form-pontuacao").validate({
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