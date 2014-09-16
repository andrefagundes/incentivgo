var Colaboradores = {
    init: function(){
        $("#btnPesquisarColaboradores").click(function(){
            Colaboradores.pesquisarColaboradores(1);
        });
        
        Colaboradores.pesquisarColaboradores(1);
    },
    pesquisarColaboradores: function(page){
        //T de todos os colaboradores
        var ativo   = 'T';
        var filter  = $("#filter").val();
       
        $('input:checkbox[name=status]').each(function() {	                
            if ($(this).is(':checked'))
                ativo = $(this).val();
        });
        
        $.post( "pesquisar-colaboradores", { 'page': page, 'ativo':ativo, 'filter':filter }, function(data){
             $( "#pesquisarColaborador" ).empty().append( data );
        },'html');
    }
};


