var PerfilEmpresa = {
    init: function(){
        
        if($("#mensagem-modal-resposta-personalizar")){
            $("#mensagem-modal-resposta-personalizar").slideUp({start:2000,duration: 3000});
        }
        var logo = '';
        if($("#logo_hidden").val()){
           logo = $("#empresaId").val()+'/logo/'+$("#logo_hidden").val();
        }else{
           logo = 'incentivgo.png';
        }
        
        $("#input-logo").fileinput({
            initialPreview: [
                '<img src=../img/users/'+logo+'>'
            ],
            overwriteInitial: true,
            previewFileType: "image",
            allowedFileExtensions: ["jpg","jpeg","gif","png"],
            maxFileSize: 100,
            maxImageWidth: 200,
            maxImageHeight: 200,
            showUpload: false,
            showCaption: false,
            showRemove: false,
            browseClass: "btn btn-facebook btn-file-logo form-control squared",
            browseLabel: "Alterar logo",
            browseIcon: '',
            msgSizeTooLarge:'A imagem "{name}" ({size} KB) é maior que o permitido {maxSize} KB.',
            msgImageWidthLarge:'A imagem deve conter no máximo {size} px de largura.',
            msgImageHeightLarge:'A imagem deve conter no máximo {size} px de altura.',
            msgFileNotFound :'Arquivo "{name}" não encontrado!',
            msgInvalidFileType :'Tipo inválido. Apenas "{types}" arquivos são suportados.',
            msgInvalidFileExtension:'Extensão inválida. Apenas "{extensions}" são suportadas.',
            elErrorContainer: "#errorBlock43"
        });
        
        $("form#form-personalizar").validate({
            rules: {
                "dados[nome]": {
                    required: true
                }
            },
            messages: {
                "dados[nome]": 'Campo obrigatório'
            }
        });
    }
};