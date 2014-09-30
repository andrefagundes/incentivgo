var Regra = {
    init: function(){
        //evento do botão de pesquisar desafio
        $("#btnPesquisarRegras").click(function(){
            Regra.pesquisarRegra(1);
        });
        
        if($("#mensagem-modal-regra")){
            $("#mensagem-modal-regra").slideUp({duration: 3000});
        }
        
        //evento do botão de criar regra
        $("#btnCriarRegra").click(function() {
            $("#myModalLabel").html('Criar Regra');
            $( "#modal-body-regra" ).html('').load( "regra/modal-regra/"+0 );
            $('#modalCriarRegra').modal('show'); 
        });

        Regra.pesquisarRegra();
    },
    pesquisarRegra: function(page){
        var ativo   = 'T';
        var filter  = $("#filterRegras").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });
        
        $.post( "regra/pesquisar-regra", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarRegras" ).empty().append( data );
             Regra.inserirQuantidadeRegra($("#quantRegra").val());
        },'html');
    },
    inserirQuantidadeRegra:function(quantRegra){
       $("#quantidade-regra").html('').html(quantRegra);
    }
};