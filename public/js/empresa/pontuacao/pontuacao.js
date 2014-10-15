var Pontuacao = {
    init: function(){
        //evento do botão de pesquisar regras de pontuações debitadas.
        $("#btnPesquisarRegrasPontuacoes").click(function(){
            Pontuacao.pesquisarPontuacao(1);
        });
        
        if($("#mensagem-modal-pontuacao")){
            $("#mensagem-modal-pontuacao").slideUp({duration: 3000});
        }
        
        //evento do botão de criar regra de pontuação
        $("#btnCriarRegraPontuacao").click(function() {
            $("#myModalLabel").html('Criar Regra Pontuacao');
            $( "#modal-body-pontuacao" ).html('').load( "pontuacao/modal-pontuacao/"+0 );
            $('#modalCriarRegraPontuacao').modal('show'); 
        });

        Pontuacao.pesquisarPontuacao();
    },
    pesquisarPontuacao: function(page){
        var ativo   = 'T';
        var filter  = $("#filterRegrasPontuacoes").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });
        
        $.post( "pontuacao/pesquisar-pontuacao", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarRegrasPontuacao" ).empty().append( data );
             Pontuacao.inserirQuantidadePontuacao($("#quantPontuacoes").val());
        },'html');
    },
    inserirQuantidadePontuacao:function(quantPontuacao){
       $("#quantidade-pontuacao").html('').html(quantPontuacao);
    }
};