var ModalAnalisarDesafio = {
    
    init: function() {
        
        var DESAFIO_TIPO_INDIVIDUAL = 1;
        var DESAFIO_TIPO_EQUIPE     = 2;
        
        if($("#hidden_id").val() == ""){
            $("#colaboradores-participantes").prop("disabled", true);
            $("#div_colab_resp").hide();
        }else{
            if($("#tipoDesafio").val() == DESAFIO_TIPO_INDIVIDUAL){
                $("#div_colab_resp").hide();
            }else if($("#tipoDesafio").val() == DESAFIO_TIPO_EQUIPE){
                $("#div_colab_resp").show();
            }
        }
        
        $("#btnReprovarDesafio").click(function(e){
            e.preventDefault();
            ModalAnalisarDesafio.aprovarReprovarDesafio('N');
        });
        $("#btnAprovarDesafio").click(function(e){
            e.preventDefault();
            ModalAnalisarDesafio.aprovarReprovarDesafio('Y');
        });

        $("#form-analisar-desafio").validate({
            rules: {
                'dados[observacao-analise]': {
                    required: true
                }
            },
            messages: {
                'dados[observacao-analise]': 'Campo obrigatório'
            }
        });
    },
    aprovarReprovarDesafio: function(resposta){
        $("#resposta").val(resposta);
        $("#form-analisar-desafio").submit();
    }
};