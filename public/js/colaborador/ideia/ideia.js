var Ideia = {
    init: function(){
        //marcar menu ativo
        $("[id^='menu-'] a").removeClass('active');
        $("#menu-ideia-colaborador a").addClass('active');
        //evento do botão de criar desafio
        $("#btnIdeias").click(function() {
            $("#myModalLabelIdeias").html('Ideias');
            $("#modal-body-ideias").html('').load( "ideia/modal-ideias/"+0 );
            $("#modalIdeias").modal('show'); 
        });
    }
};