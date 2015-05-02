var Recompensa = {
    init: function(){
        $("#btnRecompensas").click(function() {
            $("#myModalLabelRecompensas").html('Recompensas');
            $("#modal-body-recompensas").html('').load( "recompensa/modal-recompensa/"+0 );
            $("#modalRecompensas").modal('show'); 
        });
    }
};