var ModalDesafio = {
    
    init: function(lang) {
        ModalDesafio.lang = lang;
        ModalDesafio.DESAFIO_TIPO_INDIVIDUAL = 1;
        ModalDesafio.DESAFIO_TIPO_EQUIPE     = 2;

        if($("#hidden_id").val() == ""){
            $("#colaboradores-participantes").prop("disabled", true);
            $("#div_colab_resp").hide();
        }else{
            if($("#tipoDesafio").val() == ModalDesafio.DESAFIO_TIPO_INDIVIDUAL){
                $("#div_colab_resp").hide();
            }else if($("#tipoDesafio").val() == ModalDesafio.DESAFIO_TIPO_EQUIPE){
                $("#div_colab_resp").show();
            }
        }
        $.fn.modal.Constructor.prototype.enforceFocus =function(){};
        $("#tipoDesafio").change(ModalDesafio.verificarTipoDesafio);

        ModalDesafio.formataInputData();
        formataSelectColaboradores();
        formataSelectColaboradorResp();
        
        //select está hidden, por isso ignoro o default que é não validar campos hidden
        $.validator.setDefaults({
            ignore: []
        });

        $("#form-desafio").validate({
            rules: {
                debug: true,
                'dados[desafio_tipo_id]': {
                    required: true
                },
                'dados[tipo_desafio]': {
                    required: true
                },
                'dados[colaboradores-participantes][]': {
                    validarParticipantes: true
                },
                'dados[colaborador-responsavel]': {
                    validarParticipanteResponsavel: true
                },
                'dados[desafio]': {
                    required: true
                },
                'dados[data_inicio]': {
                    required: true
                },
                'dados[data_fim]': {
                    required: true
                }
            },
            messages: {
                'dados[desafio_tipo_id]': (ModalDesafio.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                'dados[tipo_desafio]': (ModalDesafio.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                'dados[desafio]': (ModalDesafio.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                'dados[data_inicio]': (ModalDesafio.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                'dados[data_fim]': (ModalDesafio.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                'dados[colaboradores-participantes][]':{
                    required:(ModalDesafio.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                    minlength:(ModalDesafio.lang === 'pt-BR' ? 'Selecione um colaborador' : 'Select a collaborator')
                },
                'dados[colaborador-responsavel]':(ModalDesafio.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required')
            }
        });
        
        jQuery.validator.addMethod("validarParticipantes", function() {
            if($("#colaboradores-participantes").val() === '' || $("#colaboradores-participantes").val() === null )
            {
                return false;
            }
            return true;
        }, (ModalDesafio.lang === 'en' ? 'Required' : 'Campo obrigatório'));
        
        jQuery.validator.addMethod("validarParticipanteResponsavel", function() {
            if($("#tipoDesafio").val() == 2 && ($("#colaborador-responsavel").val() == '' || $("#colaborador-responsavel").val() === null ) )
            {
                return false;
            }
            return true;
        }, (ModalDesafio.lang === 'en' ? 'Required' : 'Campo obrigatório'));
    },
    formataInputData: function() {

        $('.data-desafio').datetimepicker({
            locale: ModalDesafio.lang,
            format:'L',
            icons: {
                    date: "fa fa-calendar"
                }
        });
    },
    verificarTipoDesafio:function(){
        $("#colaboradores-participantes,#colaborador-responsavel").val(null).select2().select2('destroy');
        $("#colaboradores-participantes").prop("disabled", false);
        
        if($(this).val() == 1){
            formataSelectColaboradores(1);
            $("#div_colab_resp").hide();
        }else if($(this).val() == 2){
            formataSelectColaboradores(10);
            formataSelectColaboradorResp();
            $("#div_colab_resp").show();
        }else{
            $("#colaboradores-participantes").prop("disabled", true);
            $("#div_colab_resp").hide();
        }
    }
};

function formatoResultado(data) {
   return data.text;
}
function formatoSelect(data) {
    return data.text;
}

function formataSelectColaboradores(quantColaboradores) {
    $("#colaboradores-participantes").select2({
        allowClear: true,
        theme: "bootstrap",
        language:ModalDesafio.lang,
        maximumSelectionLength:quantColaboradores,
        placeholder:(ModalDesafio.lang === 'pt-BR' ? 'Selecione os colaboradores participantes...' : 'Select participants employees...'),
        minimumInputLength: 3,
        ajax: {
            url: "desafio/pesquisar-colaborador/filter/",
            dataType: 'json',
            delay:150,
            quietMillis: 250,
            data: function (params) {
                return {
                  filter: params.term, // search 
                  page_limit: 10,
                  page: params.page
                };
            },       
            processResults: function (data, page) {        
                return {
                  results: data
                };
            }
        },
        templateResult: formatoResultado,
        templateSelection: formatoSelect,
        cache:false
    });
}

function formataSelectColaboradorResp() {
    $("#colaborador-responsavel").select2({
        theme: "bootstrap",
        allowClear: true,
        language:ModalDesafio.lang,
        placeholder:(ModalDesafio.lang === 'pt-BR' ? 'Selecione o colaborador responsável...' : 'Select the employee responsible...'),
        minimumInputLength: 3,
        maximumSelectionLength: 1,
        ajax: {
            url: "desafio/pesquisar-colaborador/filter/",
            dataType: 'json',
            delay:150,
            quietMillis: 100,
            data: function (params) {
                return {
                  filter: params.term, // search term
                  page: params.page
                };
              },
            processResults: function (data, page) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                  results: data
                };
              }
        },
        templateResult: formatoResultado,
        templateSelection: formatoSelect,
        cache:false
    });
}