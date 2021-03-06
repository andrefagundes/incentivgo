var ModalDesafios = {

    init: function(lang) {
        ModalDesafios.lang = lang;
        $("[data-toggle=popover]").popover({html:true});
        $("[data-toggle=tooltip]").tooltip();
        if($("#mensagem-modal-resposta-desafio")){
            $("#mensagem-modal-resposta-desafio").slideUp({start:3000,duration: 5000});
        }
        
        this.isResposta = '';
        this.isMotivo = '';
        
        //evento do botão aceitar desafio
        $("#btnAceitarDesafio,#btnDescartarDesafio,#btnEnviarMotivoDescarte").click(ModalDesafios.responderDesafio);
        $(".btnCancelarDescarte").click(ModalDesafios.cancelarDescarte);
        $(".btnDesafioCumprido").click(ModalDesafios.desafioCumprido);
        
    },
    responderDesafio: function() {
     
        var desafioId = $(this).parent().attr('id');
        
        if ($(this).attr('id') === 'btnAceitarDesafio') {
             ModalDesafios.isResposta = 'Y';
        }else if($(this).attr('id') === 'btnDescartarDesafio'){
             $("#li_desafio_"+desafioId).hide();
             $("#li_motivo_"+desafioId).show();
             $("#form_desafio_"+desafioId).validate();
             $("#txt_motivo_"+desafioId).rules("add", {
                    required:true,
                    messages: {
                         required:(ModalDesafios.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required')
                    }
             });
             return false;
        }else if($(this).attr('id') === 'btnEnviarMotivoDescarte'){
            ModalDesafios.isResposta = 'N';
            ModalDesafios.isMotivo   = $("#txt_motivo_"+desafioId).val();
        }
        if($("#form_desafio_"+desafioId).valid()){
            $.post("responder-desafio", {'id': desafioId, 'resposta': ModalDesafios.isResposta,'motivo': ModalDesafios.isMotivo}, function() {
                $("#modal-body-desafios").html('').load("modal-desafios/" + 0);
            }, 'json');
        }
    },
    desafioCumprido: function() {
        
        var desafioId = $(this).parent().attr('id');

        $.post("desafio-cumprido", {'id': desafioId}, function() {
            $("#modal-body-desafios").html('').load("modal-desafios/" + 0);
        }, 'json');
    },
    cancelarDescarte: function(){
        var desafioId = $(this).parent().attr('id');
        $("#txt_motivo_"+desafioId).rules('remove');
        $("#li_motivo_"+desafioId).hide();
        $("#li_desafio_"+desafioId).show();
    }
}; 