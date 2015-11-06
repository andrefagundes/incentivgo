var NovaMensagem = {
    
    init: function(lang) {
        NovaMensagem.lang = lang;
        formataSelectColaboradores();
        
        $("#btnCancelarNovaMensagem").click(function(){
            ColaboradorMensagem.pesquisarMensagem(1);
            $("#grupo-filtros").show();
        });
        
         //select está hidden, por isso ignoro o default que é não validar campos hidden
        $.validator.setDefaults({
            ignore: []
        });

        $("#form-nova-mensagem").validate({
            rules: {
                'dados[destinatarios-mensagem][]': {
                    validarParticipantes: true
                },
                'dados[titulo]': {
                    required: true
                },
                'dados[mensagem]': {
                    required: true
                } 
            },
            messages: {
                'dados[destinatarios-mensagem][]':{
                    required:(NovaMensagem.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                    minlength:(NovaMensagem.lang === 'pt-BR' ? 'Selecione um destinatário' : 'Select a recipient')
                },
                'dados[titulo]': (NovaMensagem.lang === 'pt-BR' ? 'Campo obrigatório' : 'required'),
                'dados[mensagem]': (NovaMensagem.lang === 'pt-BR' ? 'Campo obrigatório' : 'required')
            }
        });
        
        jQuery.validator.addMethod("validarParticipantes", function() {
            alert($("#destinatarios-mensagem").val());
            if($("#destinatarios-mensagem").val() === '' || $("#destinatarios-mensagem").val() === null)
            {
                return false;
            }
            return true;
        }, (NovaMensagem.lang === 'pt-BR' ? 'Campo obrigatório' : 'required'));
    }
};

function formatoResultado(data) {
   return data.text;
}
function formatoSelect(data) {
    return data.text;
}

function formataSelectColaboradores() {
    $("#destinatarios-mensagem").select2({
        allowClear: true,
        theme: "bootstrap",
        language:NovaMensagem.lang,
        placeholder: (NovaMensagem.lang === 'pt-BR' ? 'Pesquise o colaborador...' : "Search collaborator..."),
        minimumInputLength: 3,
        multiple: true,
        openOnEnter:true,
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