var Ideia = {
    init: function(){

        //marcar menu ativo
        $(".dropdown-submenu").removeClass('active');
        $("#menu-ideia").addClass('active');
        
        if($("div.alert")){
            $("div.alert").slideUp({duration: 3000});
        }
        
        $("#btnPesquisarIdeias").click(function(){
            Ideia.pesquisarIdeia(1);
        });
        
        $("#btnMapearPontuacao").click(function(){
            $("#myModalLabelMapear").html('Mapear Pontuação de Ideias');
            $("#modal-body-mapear").html('').load( "ideia/modal-mapear-pontuacao" );
            $("#modalMapearPontuacao").modal('show'); 
        });
        
        Ideia.pesquisarIdeia(1);
    },
    pesquisarIdeia: function(page){

        //T de todas as ideias
        var ativo   = 'T';
        var filter  = $("#filterIdeias").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });

        $.post( "ideia/pesquisar-ideia", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarIdeia" ).empty().append( data );
        },'html');
    }
};