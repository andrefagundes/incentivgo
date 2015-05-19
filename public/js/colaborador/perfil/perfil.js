var Perfil = {
    init: function(){
        
        if($("#mensagem-modal-resposta-perfil")){
            $("#mensagem-modal-resposta-perfil").slideUp({start:2000,duration: 3000});
        }
        var avatar = '';
        if($("#avatar").val()){
           avatar = $("#empresaId").val()+'/'+$("#usuarioId").val()+'/'+$("#avatar").val();
        }else{
           avatar = 'bfdc40e956b123b24b4962cc9409485a.jpg';
        }
        
        $("#input-avatar").fileinput({
            initialPreview: [
                '<img src=../img/users/'+avatar+'>'
            ],
            overwriteInitial: true,
            previewFileType: "image",
            allowedFileExtensions: ["jpg","jpeg","bmp","gif","png"],
            maxFileSize: 1000,
            showUpload: false,
            showCaption: false,
            showRemove: false,
            browseClass: "btn btn-facebook btn-file-avatar form-control",
            browseLabel: "Alterar avatar",
            browseIcon: '',
            msgSizeTooLarge:'O arquivo "{name}" ({size} KB) é maior que o permitido {maxSize} KB.',
            msgFileNotFound :'Arquivo "{name}" não encontrado!',
            msgInvalidFileType :'Tipo inválido. Apenas "{types}" arquivos são suportados.',
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
                },
                "dados[dt_nascimento]": {
                    dateBR:true
                }
            },
            messages: {
                "dados[nome]": 'Campo obrigatório',
                "dados[email]": {required:'Campo obrigatório',email:'Email inválido'}
            }
        });
        
        jQuery.validator.addMethod("dateBR", function(value) {
           if(value.length == "") return true;
           if(value.length!=10) return false;
           var data        = value;
           var dia         = data.substr(0,2);
           var barra1      = data.substr(2,1);
           var mes         = data.substr(3,2);
           var barra2      = data.substr(5,1);
           var ano         = data.substr(6,4);
           if(data.length!=10||barra1!="/"||barra2!="/"||isNaN(dia)||isNaN(mes)||isNaN(ano)||dia>31||mes>12)return false;
           if((mes==4||mes==6||mes==9||mes==11)&&dia==31)return false;
           if(mes==2 && (dia>29||(dia==29&&ano%4!=0)))return false;
           if(ano < 1900)return false;
           return true;
       }, "Data inválida");
        
        $("#dt-nascimento").mask('##/##/####');
    }
};