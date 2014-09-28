var Colaborador = {
    init: function(){
        
        if($("#mensagem-modal-colaborador")){
            $("#mensagem-modal-colaborador").slideUp({duration: 3000});
        }
        
        $("#btnPesquisarColaboradores").click(function(){
            Colaborador.pesquisarColaborador(1);
        });
        
        //evento do botão de criar desafio
        $("#btnCriarColaborador").click(function() {
            $("#myModalLabel").html('Cadastrar Colaborador');
            $("#modal-body-colaborador").html('').load( "colaborador/modal-colaborador/"+0 );
            $("#modalCriarColaborador").modal('show'); 
        });
        
        Colaborador.pesquisarColaborador(1);
    },
    pesquisarColaborador: function(page){
        //T de todos os colaboradores
        var ativo   = 'T';
        var filter  = $("#filterColaboradores").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });

        $.post( "colaborador/pesquisar-colaborador", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarColaborador" ).empty().append( data );
        },'html');
    }
};