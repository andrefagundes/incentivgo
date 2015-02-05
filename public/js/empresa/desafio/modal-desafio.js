var ModalDesafio = {
    
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
        
        $("#tipoDesafio").change(ModalDesafio.verificarTipoDesafio);

        ModalDesafio.formataInputData();
        formataSelectColaboradores();
        formataSelectColaboradorResp();

        $("#form-desafio").validate({
            rules: {
                'dados[tipo_desafio]': {
                    required: true
                },
                'dados[colaboradores-participantes]': {
                    validarParticipantes: true
                },
                'dados[colaborador-responsavel]': {
                    validarParticipanteResponsavel: true
                },
                'dados[desafio]': {
                    required: true
                },
                'dados[pontuacao]': {
                    required: true
                },
                'dados[data_inicio]': {
                    required: true,
                    validarData: true
                },
                'dados[data_fim]': {
                    required: true,
                    validarData: true
                }
            },
            messages: {
                'dados[tipo_desafio]': 'Campo obrigatório',
                'dados[desafio]': 'Campo obrigatório',
                'dados[pontuacao]': 'Campo obrigatório',
                'dados[data_inicio]': 'Campo obrigatório',
                'dados[data_fim]': 'Campo obrigatório',
                'dados[colaboradores-participantes]':'Campo obrigatório',
                'dados[colaborador-responsavel]':'Campo obrigatório'
            }
        });
        
        jQuery.validator.addMethod("validarParticipantes", function() {
            if($("#colaboradores-participantes").val() === '')
            {
                return false;
            }
            return true;
        }, "Campo obrigatório");
        
        jQuery.validator.addMethod("validarParticipanteResponsavel", function() {
            if($("#tipoDesafio").val() == 2 && $("#colaborador-responsavel").val() == '' )
            {
                return false;
            }
            return true;
        }, "Campo obrigatório");

        jQuery.validator.addMethod("validarData", function(value, element) {
            var date = value;
            var ardt = new Array;
            var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
            ardt = date.split("/");
            erro = false;
            if (date.search(ExpReg) === -1) {
                erro = true;
            }
            else if (((ardt[1] === 4) || (ardt[1] === 6) || (ardt[1] === 9) || (ardt[1] === 11)) && (ardt[0] > 30))
                erro = true;
            else if (ardt[1] === 2) {
                if ((ardt[0] > 28) && ((ardt[2] % 4) !== 0))
                    erro = true;
                if ((ardt[0] > 29) && ((ardt[2] % 4) === 0))
                    erro = true;
            }
            if (erro) {
                element.focus();
                element.value = "";
                return false;
            }

            return true;

        }, "Data inválida");
    },
    formataInputData: function() {
        $('.datepickerDesafio').datepicker({
            language: "pt-BR",
            dateFormat: "dd/mm/yyyy",
            multidate: false,
            keyboardNavigation: false,
            autoclose: true,
            todayHighlight: true
        });
    },
    verificarTipoDesafio:function(){
        $("#colaboradores-participantes,#colaborador-responsavel").val(null).trigger("change");
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
   return data.nome;
}
function formatoSelect(data) {
    return data.nome;
}

function formataSelectColaboradores(quantColaboradores) {
    $("#colaboradores-participantes").select2({
        placeholder: "Pesquise por colaborador",
        minimumInputLength: 3,
        maximumSelectionSize: quantColaboradores,
        multiple: true,
        openOnEnter:true,
        ajax: {
            url: "desafio/pesquisar-colaborador/filter/",
            dataType: 'json',
            quietMillis: 100,
            data: function(term,page) {
                return {
                    filter: term,
                    page:page,
                    page_limit: 10
                };
            },
                     
            results: function(data,page) {
                return {results: data,more:page};
            }
        },
        initSelection: function(element,callback) {
    
            var id_colaboradores = $(element).val();
            if (id_colaboradores !== "")
            {
                $.ajax("desafio/pesquisar-colaborador/filter/?colaboradores=" + id_colaboradores, {
                    dataType: "json",
                    results: function(data) {
                        return {results: data};
                    }
                }).done(function(data) { callback(data); });     
            }
        },
        formatResult: formatoResultado,
        formatSelection: formatoSelect,
        dropdownCssClass: "bigdrop"
    });
}

function formataSelectColaboradorResp() {
    $("#colaborador-responsavel").select2({
        placeholder: "Pesquise por colaborador",
        minimumInputLength: 3,
        maximumSelectionSize: 1,
        multiple: true,
        openOnEnter:true,
        ajax: {
            url: "desafio/pesquisar-colaborador/filter/",
            dataType: 'json',
            quietMillis: 100,
            data: function(term,page) {
                return {
                    filter: term,
                    page:page,
                    page_limit: 10
                };
            },
                     
            results: function(data,page) {
                return {results: data,more:page};
            }
        },
        initSelection: function(element,callback) {
    
            var id_colaboradores = $(element).val();
            if (id_colaboradores !== "")
            {
                $.ajax("desafio/pesquisar-colaborador/filter/?colaboradores=" + id_colaboradores, {
                    dataType: "json",
                    results: function(data) {
                        return {results: data};
                    }
                }).done(function(data) { callback(data); });     
            }
        },
        formatResult: formatoResultado,
        formatSelection: formatoSelect,
        dropdownCssClass: "bigdrop"
    });
}