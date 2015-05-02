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
   return data.nome;
}
function formatoSelect(data) {
    return data.nome;
}

function formataSelectColaboradores() {
    $("#destinatarios-mensagem").select2({
        placeholder: "Pesquise o colaborador",
        minimumInputLength: 3,
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
        formatResult: formatoResultado,
        formatSelection: formatoSelect,
        dropdownCssClass: "bigdrop"
    });
}