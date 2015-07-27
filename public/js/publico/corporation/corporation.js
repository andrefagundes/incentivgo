var Corporation = {
    init: function() {
        formataSelectEmpresa();
        $("#form-corporation").validate({
            rules: {
                'dados[desafio_tipo_id]': {
                    required: true
                }
            },
            messages: {
                'dados[desafio_tipo_id]': 'Campo obrigatório'
            }
        });
    }
};

function formatoResultadoCorporation(data) {
   return data.nome;
}
function formatoSelectCorporation(data) {
    return data.nome;
}

function formataSelectEmpresa() {
    $("#empresa").select2({
        placeholder: "Pesquise sua empresa...",
        minimumInputLength: 3,
        maximumSelectionSize: 1,
        openOnEnter:true,
        ajax: {
            url: "corporation/pesquisar-empresa/filter/",
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
        formatResult: formatoResultadoCorporation,
        formatSelection: formatoSelectCorporation,
        dropdownCssClass: "bigdrop"
    });
}