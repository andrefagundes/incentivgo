var Ideia = {
    init: function(lang){
        Ideia.lang = lang;
        //marcar menu ativo
        $("[id^='menu-'] a").removeClass('active');
        $("#menu-ideia-colaborador a").addClass('active');
        //evento do botão de criar desafio
        $("#btnIdeias").click(function() {
            $("#myModalLabelIdeias").html((Ideia.lang === 'pt-BR' ? 'Ideias' : 'Ideas'));
            $("#modal-body-ideias").html('').load( "ideia/modal-ideias/"+0 );
            $("#modalIdeias").modal('show'); 
        });
    }
};