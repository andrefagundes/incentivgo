var PerfilEmpresa = {
    init: function(lang){
        PerfilEmpresa.lang = lang;
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
                '<img class="file-preview-image" style="width:100%;height:100%;" src=../img/users/'+logo+'>'
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
            browseLabel: (PerfilEmpresa.lang === 'pt-BR' ? 'Alterar avatar' : 'Change logo'),
            browseIcon: '',
            msgSizeTooLarge:(PerfilEmpresa.lang === 'pt-BR' ? 'O arquivo "{name}" ({size} KB) é maior que o permitido {maxSize} KB.' : 'The file "{name}" ({size} KB) is greater than the allowed {maxSize} KB.'),
            msgImageWidthLarge:(PerfilEmpresa.lang === 'pt-BR' ? 'A imagem deve conter no máximo {size} px de largura.' : 'The image should not exceed {size} px wide.'),
            msgImageHeightLarge:(PerfilEmpresa.lang === 'pt-BR' ? 'A imagem deve conter no máximo {size} px de altura.' : 'The image must not exceed {size} px tall.'),
            msgFileNotFound :(PerfilEmpresa.lang === 'pt-BR' ? 'Arquivo "{name}" não encontrado!' : 'File "{name}" not found!'),
            msgInvalidFileType :(PerfilEmpresa.lang === 'pt-BR' ? 'Tipo inválido. Apenas "{types}" arquivos são suportados.' : 'Invalid type. Only "{types}" files are supported.'),
            msgInvalidFileExtension:(PerfilEmpresa.lang === 'pt-BR' ? 'Extensão inválida. Apenas "{extensions}" são suportadas.' : 'Invalid extension. Only "{extensions}" are supported.'),
            elErrorContainer: "#errorBlock43"
        });
        
        $("form#form-personalizar").validate({
            rules: {
                "dados[nome]": {
                    required: true
                }
            },
            messages: {
                "dados[nome]": (PerfilEmpresa.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required')
            }
        });
    }
};