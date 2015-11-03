var NovaMensagem = {
    
    init: function(lang) {
        NovaMensagem.lang = lang;
        formataSelectColaboradores();
        
        $("#btnCancelarNovaMensagem").click(function(){
            ColaboradorMensagem.pesquisarMensagem(1);
            $("#grupo-filtros").show();
        });

        $("#form-nova-mensagem").validate({
            rules: {
                'dados[destinatarios-mensagem]': {
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
                'dados[destinatarios-mensagem]':(NovaMensagem.lang === 'en' ? 'recipient mandatory' : 'Destinatário obrigatório'),
                'dados[titulo]': (NovaMensagem.lang === 'en' ? 'mandatory title' : 'Título obrigatório'),
                'dados[mensagem]': (NovaMensagem.lang === 'en' ? 'mandatory message' : 'Mensagem obrigatória')
            }
        });
        
        jQuery.validator.addMethod("validarParticipantes", function() {
            if($("#destinatarios-mensagem").val() === '')
            {
                return false;
            }
            return true;
        }, (NovaMensagem.lang === 'en' ? 'required' : 'Campo obrigatório'));
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
        placeholder: (NovaMensagem.lang === 'en' ? 'Search collaborator...' : "Pesquise o colaborador..."),
        minimumInputLength: 3,
        multiple: true,
        openOnEnter:true,
        ajax: {
            url: "desafio/pesquisar-colaborador/filter/",
            dataType: 'json',
            quietMillis: 100,
            data: function (params) {
                return {
                  filter: params.term, // search 
                  page_limit: 10,
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
        formatResult: formatoResultado,
        formatSelection: formatoSelect,
        cache:false
    });
}