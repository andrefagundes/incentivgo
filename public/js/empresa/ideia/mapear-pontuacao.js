var ModalMapearPontuacao = {
    init: function(lang){
        ModalMapearPontuacao.lang = lang;
        $("[data-toggle=tooltip]").tooltip();
        $("form#form-mapear-pontuacao").validate({
            rules: {
                "dados[pontuacao_ideia_enviada]": {
                    required: true
                },
                "dados[pontuacao_ideia_aprovada]": {
                    required: true
                }
            },
            messages: {
                "dados[pontuacao_ideia_enviada]": (ModalMapearPontuacao.lang === 'en' ? 'Required' : 'Campo obrigatório'),
                "dados[pontuacao_ideia_aprovada]":(ModalMapearPontuacao.lang === 'en' ? 'Required' : 'Campo obrigatório')
            }
        });
    }
};