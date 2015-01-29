var ModalIdeia = {
    init: function() {
        $("[data-toggle=tooltip]").tooltip();
        
        $("#btnGuardarIdeia").click(function(e){
            e.preventDefault();
            ModalIdeia.guardarAprovarIdeia('N');
        });
        $("#btnAprovarIdeia").click(function(e){
            e.preventDefault();
            ModalIdeia.guardarAprovarIdeia('Y');
        });
        
        //matar evento de click dos botões para não duplicar quando abrir o modal novamente.
        $('#modalAnalisarIdeia').on('hidden.bs.modal', function () {
            $("#btnGuardarIdeia,#btnAprovarIdeia").off();
        });
        
    },
    guardarAprovarIdeia: function(resposta){
        $("#resposta").val(resposta);
        $("#form-analisar-ideia").submit();
    }
};