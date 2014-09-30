var Desafio = {
    init: function(){
        
        //evento do botão de pesquisar desafio
        $("#btnPesquisarDesafio").click(function(){
            Desafio.pesquisarDesafio(1);
        });
        
        if($("#mensagem-modal-desafio")){
            $("#mensagem-modal-desafio").slideUp({start:3000,duration: 5000});
        }
        
        //evento do botão de criar desafio
        $("#btnCriarDesafio").click(function() {
            $("#myModalLabel").html('Criar Desafio');
            $( "#modal-body-desafio" ).html('').load( "desafio/modal-desafio/"+0 );
            $('#modalCriarDesafio').modal('show'); 
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
             Desafio.inserirQuantidadeDesafio($("#quantDesafio").val());
        },'html');
    },
    inserirQuantidadeDesafio:function(quantDesafio){
       $("#quantidade-desafio").html('').html(quantDesafio);
    }
};