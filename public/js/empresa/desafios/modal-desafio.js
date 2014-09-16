var ModalDesafio = {
    init: function() {

        ModalDesafio.formataInputData();
        formataSelectColaboradores();

        $("#form-desafio").validate({
            rules: {
                'dados[colaboradores-participantes]': {
                    validarParticipantes: true
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
                'dados[desafio]': 'Campo obrigatório',
                'dados[pontuacao]': 'Campo obrigatório',
                'dados[data_inicio]': 'Campo obrigatório',
                'dados[data_fim]': 'Campo obrigatório',
                'dados[colaboradores-participantes]':'Campo obrigatório'
            }
        });
        
        jQuery.validator.addMethod("validarParticipantes", function() {
            if($("#colaboradores-participantes").val() === '')
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
    }
};

function formatoResultado(data) {
   return data.nome;
}
function formatoSelect(data) {
    return data.nome;
}

function formataSelectColaboradores() {
    $("#colaboradores-participantes").select2({
        placeholder: "Pesquise por colaborador",
        minimumInputLength: 3,
        maximumSelectionSize: 10,
        multiple: true,
        openOnEnter:true,
        ajax: {
            url: "/incentiv/empresa/pesquisar-colaboradores/filter/",
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
                $.ajax("/incentiv/empresa/pesquisar-colaboradores/filter/?colaboradores=" + id_colaboradores, {
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