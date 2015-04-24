var UtilizarRecompensa = {
    init: function() {
        
         //marcar menu ativo
        $(".dropdown-submenu").removeClass('active');
        $("#menu-recompensa").addClass('active');
        
        if($("div.alert")){
            $("div.alert").slideUp({start:3000,duration: 5000});
        }
        
        $("#form-debito-recompensa").validate({
            rules: {
                'dados[usuario_id]': {
                    required: true
                },
                'dados[recompensa_id]': {
                    required: true
                }
            },
            messages: {
                'dados[usuario_id]': 'Campo obrigatório',
                'dados[recompensa_id]': 'Campo obrigatório'
            }
        });
    }
};