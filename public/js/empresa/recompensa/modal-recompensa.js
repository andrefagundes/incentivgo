var ModalRecompensa = {
    init: function() {
        $("#form-recompensa").validate({
            rules: {
                'dados[recompensa]': {
                    required: true
                },
                'dados[pontuacao]': {
                    required: true
                }
            },
            messages: {
                'dados[recompensa]': 'Campo obrigatório',
                'dados[pontuacao]': 'Campo obrigatório'
            }
        });
    }
};