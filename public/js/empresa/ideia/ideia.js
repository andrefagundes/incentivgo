var Ideia = {
    init: function(){
        
        if($("#mensagem-modal-ideia")){
            $("#mensagem-modal-ideia").slideUp({duration: 3000});
        }
        
        $("#btnPesquisarIdeias").click(function(){
            Ideia.pesquisarIdeia(1);
        });
        
        //evento do botão de criar desafio
        $("#btnCriarIdeia").click(function() {
            $("#myModalLabel").html('Cadastrar Ideia');
            alert('df');
            $("#modal-body-ideia").html('').load( "ideia/modal-ideia/"+0 );
            $("#modalCriarIdeia").modal('show'); 
        });
        
        Ideia.pesquisarIdeia(1);
    },
    pesquisarIdeia: function(page){

        //T de todos os ideias
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