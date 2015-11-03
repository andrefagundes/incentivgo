var ModalRecompensa = {
    init: function(lang) {
        ModalRecompensa.lang = lang;
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
                'dados[recompensa]': (ModalRecompensa.lang === 'en' ? 'Required' : 'Campo obrigatório'),
                'dados[pontuacao]': (ModalRecompensa.lang === 'en' ? 'Required' : 'Campo obrigatório')
            }
        });
    }
};