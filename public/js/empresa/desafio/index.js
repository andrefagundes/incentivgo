var Index = {
    init: function(){
        
        //marcar menu ativo
        $(".dropdown-submenu").removeClass('active');
        $("#menu-geral").addClass('active');
        
        if($("div.alert")){
            $("div.alert").slideUp({duration: 3000});
        }
        
        $(".btnAnalisar").click(function(){
            id = $(this).attr('id');
            if(id){
                $("#myModalLabelDesafios").html('Analisar Desafio');
                $("#modal-body-analisar-desafio").html('').load("desafio/modal-analisar-desafio/" + id);
                $("#modalAnalisarDesafio").modal('show');
            }
        });
    },
    aprovarIdeia: function(){

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