var NovaMensagem = {
    
    init: function(lang) {
        NovaMensagem.lang = lang;
        formataSelectColaboradores();
        $("#btnCancelarNovaMensagem").click(function(){
            Mensagem.pesquisarMensagem(1);
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
                'dados[destinatarios-mensagem][]':(NovaMensagem.lang === 'en' ? 'recipient mandatory' : 'Destinatário obrigatório'),
                'dados[titulo]': (NovaMensagem.lang === 'en' ? 'mandatory title' : 'Título obrigatório'),
                'dados[mensagem]': (NovaMensagem.lang === 'en' ? 'mandatory message' : 'Mensagem obrigatória')
            }
        });
        
        jQuery.validator.addMethod("validarParticipantes", function() {

            if($("#destinatarios-mensagem").val() === '' || $("#destinatarios-mensagem").val() === null )
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
                  page: params.page
                };
            },       
            processResults: function (data, page) {            
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