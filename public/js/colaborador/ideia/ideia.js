var Ideia = {
    init: function(){
        //evento do botão de criar desafio
        $("#btnIdeias").click(function() {
            $("#myModalLabel").html('Cadastrar Ideia');
            $("#modal-body-ideias").html('').load( "ideia/modal-ideias/"+0 );
            $("#modalIdeias").modal('show'); 
        });
    }
};