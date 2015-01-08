var Ideia = {
    init: function(){
        //evento do botão de criar desafio
        $("#btnIdeias").click(function() {
            $("#myModalLabelIdeias").html('Ideias');
            $("#modal-body-ideias").html('').load( "ideia/modal-ideias/"+0 );
            $("#modalIdeias").modal('show'); 
        });
    }
};