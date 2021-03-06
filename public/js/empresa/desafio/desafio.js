var Desafio = {
    init: function(lang){
        Desafio.lang = lang;
        //marcar menu ativo
        $(".dropdown-submenu").removeClass('active');
        $("#menu-desafio").addClass('active');
        
        //evento do botão de pesquisar desafio
        $("#btnPesquisarDesafio").click(function(){
            Desafio.pesquisarDesafio(1);
        });
        
        if($("div.alert")){
            $("div.alert").slideUp({start:3000,duration: 5000});
        }
        
        //evento do botão de criar desafio
        $("#btnCriarDesafio").click(function() {
            $("#myModalLabel").html((Desafio.lang === 'en' ? 'Create Challenge' : 'Criar Desafio'));
            $( "#modal-body-desafio" ).html('').load( "desafio/modal-desafio/"+0 );
            $('#modalCriarDesafio').modal('show'); 
        });
        
        $("#btnMapearPontuacaoDesafio").click(function(){
            $("#myModalLabelMapearDesafio").html((Desafio.lang === 'en' ? 'Map Challenges Score' : 'Mapear Pontuação de Desafios'));
            $("#modal-body-mapear-desafio").html('').load( "desafio/modal-mapear-pontuacao-desafio" );
            $("#modalMapearPontuacaoDesafio").modal('show'); 
        });

        Desafio.pesquisarDesafio();
    },
    pesquisarDesafio: function(page){
        var ativo   = 'T';
        var filter  = $("#filterDesafio").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });
        
        $.post( "desafio/pesquisar-desafio", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarDesafio" ).empty().append( data );
        },'html');
    }
};