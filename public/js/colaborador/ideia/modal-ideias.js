var ModalIdeias = {
    
    init: function() {
        
        $("#form-ideia").validate({
            rules: {
                'txt_ideia': {
                    required: true
                }
            },
            messages: {
                'txt_ideia': 'Campo obrigatório'
            }
        });
        
        if($("#mensagem-modal-resposta-ideia")){
            $("#mensagem-modal-resposta-ideias").slideUp({start:3000,duration: 5000});
        }
        
        $("#btnEnviarIdeia").click(ModalIdeias.ideia);
        $(".btnExcluirIdeia").click(ModalIdeias.excluirIdeia);
        $("#btnSalvarIdeia").click(ModalIdeias.salvarIdeia);
        $("#btnCancelarIdeia").click(ModalIdeias.cancelarIdeia);   
    },
    ideia:function(){
        $(".ul-ideias").hide();
        $("#ul_ideia").show();
    },
    salvarIdeia:function(){
        if($("#form-ideia").valid()){
            var ideia = $("#txt_ideia").val();
            $.post("ideia/salvar-ideia", {'descricao':ideia }, function() {
                $("#modal-body-ideias").html('').load( "ideia/modal-ideias/"+0 );
            }, 'json');
        }
    },
    excluirIdeia:function(){            
        var ideiaId = $(this).parent().attr('id');

        $.post("ideia/excluir-ideia", {'ideiaId':ideiaId }, function() {
            $("#modal-body-ideias").html('').load( "ideia/modal-ideias/"+0 );
        }, 'json');
    },
    cancelarIdeia: function(){
        $("#modal-body-ideias").html('').load( "ideia/modal-ideias/"+0 );
    }
}; 