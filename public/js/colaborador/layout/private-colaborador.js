var PrivateColaborador = {

    init: function() {
         // $("#div-chat").html('').load('colaborador/chat'); 
          $("#div-chat").html('').load('/incentiv/colaborador/chat'); 
          
          $('input.form-control').on('keypress',function(e) {
            if(e.which == 13) {
               alert( $(this).parent('div').attr('id'));
            }
         });
        
//        $(".btnBody").click(PrivateColaborador.loadBody);
        
//        PrivateColaborador.iniciarNode();
    },
    
    loadBody:function(){
        if($(this).attr('id')=='geral'){
            $("#div-body").html('').load('colaborador'); 
        }else if($(this).attr('id')=='ideia'){
            $("#div-body").html('').load('colaborador/ideia');
        }
    }
}; 