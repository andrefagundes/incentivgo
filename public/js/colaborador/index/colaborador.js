var Colaborador = {
    init: function(){
        
        //evento do botão encarar desafios
        $("#btnEncarar").click(function() {
            $("#myModalLabelDesafios").html('Desafios');
            $("#modal-body-desafios").html('').load( "colaborador/modal-desafios/"+0 );
            $("#modalDesafios").modal('show'); 
        });
        
        //evento do botão pedir ajuda
        $("#btnAjuda").click(function() {
            $("#myModalLabelAjudas").html('Ajudas');
            $("#modal-body-ajudas").html('').load( "colaborador/modal-ajudas/"+0 );
            $("#modalAjudas").modal('show'); 
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