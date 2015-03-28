var Colaborador = {
    init: function(){
        
        //marcar menu ativo
        $(".dropdown-submenu").removeClass('active');
        $("#menu-colaborador").addClass('active');
        
        if($("div.alert")){
            $("div.alert").slideUp({start:3000,duration: 5000});
        }
        
        $("#btnPesquisarColaboradores").click(function(){
            Colaborador.pesquisarColaborador(1);
        });
        
        //evento do botão de criar desafio
        $("#btnCriarColaborador").click(function() {
            $("#myModalLabel").html('Cadastrar Usuário');
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