var Recompensa = {
    init: function(){
        
        //marcar menu ativo
        $(".dropdown-submenu").removeClass('active');
        $("#menu-recompensa").addClass('active');
        
        //evento do botão de pesquisar desafio
        $("#btnPesquisarRecompensas").click(function(){
            Recompensa.pesquisarRecompensa(1);
        });
        
        if($("div.alert")){
            $("div.alert").slideUp({start:3000,duration: 5000});
        }
        
        //evento do botão de criar recompensa
        $("#btnCriarRecompensa").click(function() {
            $("#myModalLabel").html('Criar Regra de Uso');
            $( "#modal-body-recompensa" ).html('').load( "recompensa/modal-recompensa/"+0 );
            $('#modalCriarRecompensa').modal('show'); 
        });

        Recompensa.pesquisarRecompensa();
    },
    pesquisarRecompensa: function(page){
        var ativo   = 'T';
        var filter  = $("#filterRecompensas").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });
        
        $.post( "recompensa/pesquisar-recompensa", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarRecompensas" ).empty().append( data );
        },'html');
    }
};