var Perfil = {
    init: function(lang){
        Perfil.lang = lang;
        Perfil.formataInputData();
        if($("#mensagem-modal-resposta-perfil")){
            $("#mensagem-modal-resposta-perfil").slideUp({start:2000,duration: 3000});
        }
        var avatar = '';
        if($("#avatar").val()){
           avatar = $("#empresaId").val()+'/'+$("#usuarioId").val()+'/'+$("#avatar").val();
        }else{
           avatar = '60_bfdc40e956b123b24b4962cc9409485a.jpg';
        }
        
        $("#input-avatar").fileinput({
            initialPreview: [
                '<img class="file-preview-image" style="width:100%;height:100%;" src=../img/users/'+avatar+'>'
            ],
            language: Perfil.lang,
            overwriteInitial: true,
            previewFileType: "image",
            allowedFileExtensions: ["jpg","jpeg","gif","png"],
            maxFileSize: 1000,
            showUpload: false,
            showCaption: false,
            showRemove: false,
            browseClass: "btn btn-facebook btn-file-avatar form-control squared",
            browseLabel: (Perfil.lang === 'pt-BR' ? 'Alterar avatar' : 'Change avatar'),
            browseIcon: '',
            msgSizeTooLarge:(Perfil.lang === 'pt-BR' ? 'O arquivo "{name}" ({size} KB) é maior que o permitido {maxSize} KB.' : 'The file "{name}" ({size} KB) is greater than the allowed {maxSize} KB.'),
            msgFileNotFound :(Perfil.lang === 'pt-BR' ? 'Arquivo "{name}" não encontrado!' : 'File "{name}" not found!'),
            msgInvalidFileType :(Perfil.lang === 'pt-BR' ? 'Tipo inválido. Apenas "{types}" arquivos são suportados.' : 'Invalid type. Only "{types}" files are supported.'),
            msgInvalidFileExtension:(Perfil.lang === 'pt-BR' ? 'Extensão inválida. Apenas "{extensions}" são suportadas.' : 'Invalid extension. Only "{extensions}" are supported.'),
            elErrorContainer: "#errorBlock43"
        });
        
        $("form#form-perfil").validate({
            rules: {
                "dados[nome]": {
                    required: true
                },
                "dados[email]": {
                    required: true,
                    email:true
                }
            },
            messages: {
                "dados[nome]": (Perfil.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                "dados[email]": {
                    required:(Perfil.lang === 'pt-BR' ? 'Campo obrigatório' : 'Required'),
                    email:(Perfil.lang === 'pt-BR' ? 'Email inválido' : 'Invalid email')}
            }
        });
        
    },
    formataInputData: function() {
        $('#dt-nascimento').datetimepicker({
            locale: Perfil.lang,
            format:'L',
            icons: {
                    date: "fa fa-calendar"
                }
        });
    }
};