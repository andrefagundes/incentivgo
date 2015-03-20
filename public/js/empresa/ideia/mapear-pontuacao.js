var ModalMapearPontuacao = {
    init: function(){
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
                "dados[pontuacao_ideia_enviada]": 'Campo obrigatório',
                "dados[pontuacao_ideia_aprovada]":'Campo obrigatório'
            }
        });
    }
};