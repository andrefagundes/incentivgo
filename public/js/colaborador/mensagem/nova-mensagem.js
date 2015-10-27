var NovaMensagem = {
    
    init: function() {
        
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
                'dados[destinatarios-mensagem]':'Destinatário obrigatório',
                'dados[titulo]': 'Título obrigatório',
                'dados[mensagem]': 'Mensagem obrigatória'
            }
        });
        
        jQuery.validator.addMethod("validarParticipantes", function() {
            if($("#destinatarios-mensagem").val() === '')
            {
                return false;
            }
            return true;
        }, "Campo obrigatório");
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
        placeholder: "Pesquise o colaborador",
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