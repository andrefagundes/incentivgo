var Recompensa = {
    init: function(){
        //marcar menu ativo
        $("[id^='menu-'] a").removeClass('active');
        $("#menu-recompensa-colaborador a").addClass('active');
        $("#btnRecompensas").click(function() {
            $("#myModalLabelRecompensas").html('Recompensas');
            $("#modal-body-recompensas").html('').load( "recompensa/modal-recompensa/"+0 );
            $("#modalRecompensas").modal('show'); 
        });
    }
};