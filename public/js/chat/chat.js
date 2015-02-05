var Chat = {
    init: function(){
        
        Chat.container        = $('.chat-window-container');
        Chat.usuarioLogado    = $("#idUsuario").val();
        
        // Criando a conexão com o Socket.io
        Chat.socket = io.connect('http://localhost:8088',{
             'reconnect': true
            ,'reconnection delay': 500
            ,'max reconnection attempts': 1000
            ,'secure': false
        });
        
        Chat.informarUsuarioLogado();
        Chat.iniciarEscutasNodeJs();

        $(window).bind('enterBreakpoint480', function () {
            $('.chat-window-container .panel:not(:last)').remove();
            $('.chat-window-container .panel').attr('id', 'chat-0001');
        });

        $(window).bind('enterBreakpoint768', function () {
            if ($('.chat-window-container .panel').length == 3) {
                $('.chat-window-container .panel:first').remove();
                $('.chat-window-container .panel:first').attr('id', 'chat-0001');
                $('.chat-window-container .panel:last').attr('id', 'chat-0002');
            }
        });
        
        // remove window
        $("body").on('click', ".chat-window-container .close", function () {
            $(this).parent().parent().remove();
            Chat.chatLayout();
            if ($(window).width() < 768) $('.chat-window-container').hide();
        });
        
        // Chat heading collapse window
        $('body').on('click', '.chat-window-container .panel-heading', function (e) {
            e.preventDefault();
            $(this).parent().find('> .panel-body').toggleClass('display-none');
            $(this).parent().find('> input').toggleClass('display-none');
        });
        
        $('[data-toggle="chat-box"]').on('click', function () {
            $(".chat-contacts li:first").trigger('click');
            if ($(this).data('hide')) $(this).hide();
        });
        
        $('[data-toggle="sidebar-chat"]').on('click', function () {
            $('html').toggleClass('show-chat');
            Chat.checkChat();
        });
        
        $("div .chat-contacts").on('click', function(){
            id = $(this).data('userId');
            Chat.criarSalaChat(id);  
        });
        
        Chat.checkChat();
    },
    iniciarEscutasNodeJs:function(){
        //escutando para saber o usuário que logou
        Chat.socket.on('addUsuarioConectado', function(usuario){
            $("#user_"+usuario.idUsuario).addClass( "online" );
            $("#user_"+usuario.idUsuario).removeClass( "offline" );
        });
        
        //escutando para saber o usuário que saiu
        Chat.socket.on('removeUsuarioConectado', function(usuario){
            $("#user_"+usuario.idUsuario).removeClass( "online" );
            $("#user_"+usuario.idUsuario).addClass( "offline" );
        });
        
        //escutando para verificar se o remetente já está aberto
        Chat.socket.on('verificar-chat-aberto', function (dados) {
            Chat.canal = dados.canal;
            //verifica se o destinatario é o usuario que está logado
            if(dados.idDestinatario == Chat.usuarioLogado)
            {   //verifica se sala do rementente(que enviou a mensagem está fechada, senão cria e abre.
                if($('.chat-window-container [data-user-id="' + dados.idRemetente + '"]').length == 0){
                    Chat.criarSalaChat(dados.idRemetente);
                }else{
                    if($("#sala_"+dados.idRemetente).val() != Chat.canal){
                        Chat.socket.emit('criarCanalChat',{canal:Chat.canal,usuario:dados.idRemetente},function(data){
                            Chat.setarCanalChat(data);
                            //corrigindo atraso quando se muda de sala com F5
                            Chat.socket.emit('send-client', dados);
                        });
                    }
                }
            }
         });
         
        //escutando para receber mensagem de usuario que vem do server
        Chat.socket.on('send-client', function (dados) {
            var message = Chat.templateMessage(dados);
            $('.chat-window-container input[value="'+dados.canal+'"]').parent().append(message);
            $('.panel-body span:last-child').scrollTo(50).bottom;
        });
    },
    informarUsuarioLogado:function(){
         // Emitindo o evento para o Server
        Chat.socket.emit('usuarioConectado',{idUsuario:Chat.usuarioLogado});
    },
    enviarMensagem:function(idDestinatario,idRemetente,mensagem,canal){
        Chat.boolDestinatario = false;
        
        if($("#sala_"+idDestinatario).parent().text() == ''){
           Chat.boolDestinatario = true;
        }
 
        Chat.socket.emit('send-server', {idDestinatario: idDestinatario,
                                         idRemetente:idRemetente, 
                                         msg: mensagem,
                                         canal:canal,
                                         boolDestinatario:Chat.boolDestinatario});
    },   
    criarSalaChat:function(idUser){

        if ($('.chat-window-container [data-user-id="' + idUser + '"]').length) return;
        if ($("#user_"+idUser).attr('class') === 'desc chat-contacts offline') return;

        var context = {user_image: $("#user_"+idUser).find('img').attr('src'), user: $("#user_"+idUser).find('.contact-name').text(),id_sala:idUser};
        var html = Chat.templateChat(context);

        var clone = $(html);

        clone.attr("data-user-id", idUser);

        Chat.container.find('.panel:not([id^="chat"])').remove();

        var count = Chat.container.find('.panel').length;

        count ++;
        var limit = $(window).width() > 768 ? 3 : 1;
        if (count >= limit) {
            Chat.container.find('#chat-000'+ limit).remove();
            count = limit;
        }

        clone.attr('id', 'chat-000' + parseInt(count));
        Chat.container.append(clone).show();

        clone.show();
        clone.find('> .panel-body').removeClass('display-none');
        clone.find('> input').removeClass('display-none');

        Chat.socket.emit('criarCanalChat',{canal:Chat.canal,usuario:idUser},function(data){
            Chat.setarCanalChat(data);
        });
        
        $('#chat-000' + parseInt(count)+ ' input.form-control').focus();
        
        //evento de enter do chat
        $('input.form-control').on('keypress',function(e) {
            if(e.which == 13) {
                if($(this).val() != ""){
                    var idDestinatario   = $(this).data('sala');
                    var canal            = $("#sala_"+$(this).data('sala')).val();
                    var idRemetente      = Chat.usuarioLogado;
                    var text             = $(this).val();
                    Chat.enviarMensagem(idDestinatario,idRemetente,text,canal);
                    $(this).val('').focus();
                }
            }
        });
    },
    setarCanalChat:function(data){
        $("#sala_"+data.usuario).val(data.canal);
    },
    templateChat:function(context){
        var template = '<div class="panel panel-default">'+
                            '<div class="panel-heading" data-toggle="chat-collapse" data-target="#chat-bill">'+
                                '<a href="#" class="close"><i class="fa fa-times"></i></a>'+
                                '<a href="#">'+
                                    '<img src="'+context.user_image+'" width="40" class="pull-left">'+
                                    '<span class="contact-name">'+context.user+'</span>'+
                                '</a>'+
                            '</div>'+
                            '<div class="panel-body" id="chat-bill">'+
                            '<input type="hidden" id="sala_'+context.id_sala+'"  value="">'+
                            '</div>'+
                            '<input type="text" class="form-control" placeholder="Digite a mensagem..." data-sala='+context.id_sala+' />'+
                        '</div>';
         return template;
    },
    templateMessage:function(dados){
        var posicao = '';
        var message = '';
        if(Chat.usuarioLogado == dados.idRemetente){
            posicao = 'right';
            message = '<div class="media '+posicao+'">'+
                            '<div class="media-body">'+
                                '<span class="message">'+dados.msg+'</span>'+
                            '</div>'+
                        '</div>';
        }else{
            posicao = 'left';
            var user_image = $("#user_"+dados.idRemetente).find('img').attr('src');
            message = '<div class="media '+posicao+'">'+
                            '<div class="pull-'+posicao+'">'+
                                '<img src="'+user_image+'" width="35" height="35" class="img-circle" alt="people" />'+
                            '</div>'+
                            '<div class="media-body">'+
                                '<span class="message">'+dados.msg+'</span>'+
                            '</div>'+
                        '</div>';
        }

          return message;
    },
    chatLayout:function(){
        Chat.container.find('.panel').each(function (index, value) {
            $(this).attr('id', 'chat-000' + parseInt(index + 1));
        });
    },
    checkChat:function() {
        if (! $('html').hasClass('show-chat')) {
            $('.chat-window-container .panel-body').addClass('display-none');
            $('.chat-window-container input').addClass('display-none');
        } else {
            $('.chat-window-container .panel-body').removeClass('display-none');
            $('.chat-window-container input').removeClass('display-none');
        }
    }
};