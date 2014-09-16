var CriarDesafios = {
    init: function() {
        
        CriarDesafios.formataInputData();

        $("#criar-desafio").validate({
            rules: {
                '[dados][nome]': {
                    required: true
                },
                '[dados][data_inicio]': {
                    required: true,
                    validarData: true
                },
                '[dados][data_fim]': {
                    required: true,
                    validarData: true
                }
            },
            messages: {
                '[dados][nome]': 'Campo obrigatório',
                '[dados][data_inicio]': 'Campo obrigatório',
                '[dados][data_fim]': 'Campo obrigatório'
            }
        });

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

        $("#btnCriarRegra").click(function() {
            $('#modalCriarRegras').modal('show');  
        });
    },
    formataInputData: function() {
        $('.datepicker').datepicker({
            language: "pt-BR",
            dateFormat: "dd/mm/yy",
            multidate: false,
            keyboardNavigation: false,
            forceParse: true,
            calendarWeeks: true,
            autoclose: true,
            todayHighlight: true
        });
    }
};