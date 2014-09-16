var Desafios = {
    init: function(){
        //evento do botão de pesquisar desafio
        $("#btnPesquisarDesafios").click(function(){
            Desafios.pesquisarDesafios(1);
        });
        
        if($("#mensagem-modal-desafio")){
            $("#mensagem-modal-desafio").slideUp({duration: 3000});
        }
        
        //evento do botão de criar desafio
        $("#btnCriarDesafio").click(function() {
            $("#myModalLabel").html('Criar Desafio');
            $( "#modal-body-desafio" ).html('').load( "modal-desafio/"+0 );
            $('#modalCriarDesafio').modal('show'); 
        });

        Desafios.pesquisarDesafios();
    },
    pesquisarDesafios: function(page){
        var ativo   = 'T';
        var filter  = $("#filterDesafios").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });
        
        $.post( "pesquisar-desafios", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarDesafios" ).empty().append( data );
             Desafios.inserirQuantidadeDesafios($("#quantDesafios").val());
        },'html');
    },
    inserirQuantidadeDesafios:function(quantDesafios){
       $("#quantidade-desafios").html('').html(quantDesafios);
    }
};