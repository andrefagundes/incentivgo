var Corporation = {
    init: function(lang) {
        Corporation.lang = lang;
        formataSelectEmpresa();
    }
};

function formatoResultadoCorporation(data) {
   return data.nome;
}
function formatoSelectCorporation(data) {
    if(data.nome){
        return data.nome;
    }else{
        return (Corporation.lang === 'en' ? 'Enter your company...' : 'Informe sua empresa...');
    }
}

function formataSelectEmpresa() {
    $("#empresa").select2({
        language:Corporation.lang,
        minimumInputLength: 3,
        maximumSelectionSize: 1,
        delay: 250,
        ajax: {
            url: "corporation/pesquisar-empresa/filter/",
            dataType: 'json',
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
        templateResult: formatoResultadoCorporation,
        templateSelection: formatoSelectCorporation,
        cache:true
    });
}