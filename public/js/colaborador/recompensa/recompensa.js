var Recompensa = {
    init: function(lang){
        Recompensa.lang = lang;
        //marcar menu ativo
        $("[id^='menu-'] a").removeClass('active');
        $("#menu-recompensa-colaborador a").addClass('active');
        $("#btnRecompensas").click(function() {
            $("#myModalLabelRecompensas").html((Recompensa.lang === 'pt-BR' ? 'Recompensas' : 'Rewards'));
            $("#modal-body-recompensas").html('').load( "recompensa/modal-recompensa/"+0 );
            $("#modalRecompensas").modal('show'); 
        });
    }
};