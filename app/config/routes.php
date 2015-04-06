<?php
/*
 * Defini rotas personalizadas. Arquivo é incluído na definição do serviço roteador.
 */
$router = new Phalcon\Mvc\Router(false);

$router->setDefaultModule("publico");

$router->add('/show401', array(
    'module' => 'publico',
    'controller' => 'errors',
    'action' => 'show401'
));
$router->add('/show404', array(
    'module' => 'publico',
    'controller' => 'errors',
    'action' => 'show404'
));
$router->add('/show500', array(
    'module' => 'publico',
    'controller' => 'errors',
    'action' => 'show500'
));

$router->add('/session/logout', array(
    'module' => 'publico',
    'controller' => 'session',
    'action' => 'logout'
));

/******Rotas módulo admin**********/
$router->add('/admin', array(
    'namespace' => 'Admin\Controllers',
    'module' => 'admin',
    'controller' => 'usuario',
    'action' => 'index'
));

/******Rotas módulo público**********/
$router->add('/session/login', array(
    'controller' => 'session',
    'action' => 'login'
));

$router->add('/session/esqueceuSenha', array(
    'controller' => 'session',
    'action' => 'esqueceuSenha'
));

$router->add('/session/enviarSugestao', array(
    'controller' => 'session',
    'action' => 'enviarSugestao'
));

$router->add('/session/cadastro', array(
    'controller' => 'session',
    'action' => 'cadastro'
));

$router->add('/session/mensagem', array(
    'controller' => 'session',
    'action' => 'mensagem'
));

$router->add('/confirm/{code}/{email}', array(
    'controller' => 'usuario_control',
    'action' => 'confirmEmail'
));

$router->add('/altera-senha/{code}/{email}', array(
    'controller' => 'usuario_control',
    'action' => 'resetPassword'
));

$router->add('/enviar-contato', array(
    'controller' => 'index',
    'action' => 'contato'
));

$router->add('/contato', array(
    'controller' => 'index',
    'action' => 'contato'
));

/******Rotas módulo empresa**********/

$router->add('/empresa', array(
    'module' => 'empresa',
    'controller' => 'empresa_geral'
));

$router->add('/empresa/geral', array(
    'module' => 'empresa',
    'controller' => 'empresa_geral'
));

$router->add('/empresa/mensagens', array(
    'module' => 'empresa',
    'controller' => 'empresa_mensagem',
    'action' => 'mensagem'
));

$router->add('/empresa/mensagem/pesquisar-mensagem', array(
    'module' => 'empresa',
    'controller' => 'empresa_mensagem',
    'action' => 'pesquisarMensagem'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});
$router->add('/empresa/mensagem/nova-mensagem', array(
    'module' => 'empresa',
    'controller' => 'empresa_mensagem',
    'action' => 'novaMensagem'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});
$router->add('/empresa/mensagem/ler-mensagem/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa_mensagem',
    'action' => 'lerMensagem'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});
$router->add('/empresa/mensagem/verificar-mensagens', array(
    'module' => 'empresa',
    'controller' => 'empresa_mensagem',
    'action' => 'verificarMensagens'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});
$router->add('/empresa/mensagem/excluir-mensagem', array(
    'module' => 'empresa',
    'controller' => 'empresa_mensagem',
    'action' => 'excluirMensagem'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});
$router->add('/empresa/mensagem/salvar-mensagem', array(
    'module' => 'empresa',
    'controller' => 'empresa_mensagem',
    'action' => 'salvarMensagem'
));
$router->add('/empresa/mensagem/responder-mensagem', array(
    'module' => 'empresa',
    'controller' => 'empresa_mensagem',
    'action' => 'responderMensagem'
));

/******Rotas módulo empresa/desafio**********/

$router->add('/empresa/desafio', array(
    'module' => 'empresa',
    'controller' => 'empresa_desafio',
    'action' => 'desafio'
));

$router->add('/empresa/desafio/pesquisar-desafio', array(
    'module' => 'empresa',
    'controller' => 'empresa_desafio',
    'action' => 'pesquisarDesafio'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/desafio/modal-desafio/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa_desafio',
    'action' => 'modalDesafio'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/empresa/desafio/pesquisar-colaborador/filter/{get}', array(
    'module' => 'empresa',
    'controller' => 'empresa_desafio',
    'action' => 'pesquisarColaboradoresDesafio',
    'get' => 1
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/desafio/salvar-desafio', array(
    'module' => 'empresa',
    'controller' => 'empresa_desafio',
    'action' => 'salvarDesafio'
));

$router->add('/empresa/desafio/analisar-desafio', array(
    'module' => 'empresa',
    'controller' => 'empresa_desafio',
    'action' => 'analisarDesafio'
));

$router->add('/empresa/desafio/ativar-inativar-desafio/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'empresa_desafio',
    'action' => 'ativarInativarDesafio'
));

$router->add('/empresa/desafio/modal-analisar-desafio/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa_desafio',
    'action' => 'modalAnalisarDesafio'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

/******Rotas módulo empresa/noticia**********/

$router->add('/empresa/noticia', array(
    'module' => 'empresa',
    'controller' => 'empresa_noticia',
    'action' => 'noticia'
));

$router->add('/empresa/noticia/pesquisar-noticia', array(
    'module' => 'empresa',
    'controller' => 'empresa_noticia',
    'action' => 'pesquisarNoticia'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/noticia/modal-noticia/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa_noticia',
    'action' => 'modalNoticia'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/empresa/noticia/salvar-noticia', array(
    'module' => 'empresa',
    'controller' => 'empresa_noticia',
    'action' => 'salvarNoticia'
));

$router->add('/empresa/noticia/ativar-inativar-noticia/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'empresa_noticia',
    'action' => 'ativarInativarNoticia'
));


/******Rotas módulo empresa/regra**********/

$router->add('/empresa/regra', array(
    'module' => 'empresa',
    'controller' => 'empresa_regra',
    'action' => 'regra'
));

$router->add('/empresa/regra/pesquisar-regra', array(
    'module' => 'empresa',
    'controller' => 'empresa_regra',
    'action' => 'pesquisarRegra'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/regra/modal-regra/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa_regra',
    'action' => 'modalRegra'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/empresa/regra/salvar-regra', array(
    'module' => 'empresa',
    'controller' => 'empresa_regra',
    'action' => 'salvarRegra'
));

$router->add('/empresa/regra/ativar-inativar-regra/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'empresa_regra',
    'action' => 'ativarInativarRegra'
));
/******Rotas módulo empresa/pontuacao**********/

$router->add('/empresa/pontuacao', array(
    'module' => 'empresa',
    'controller' => 'empresa_pontuacao',
    'action' => 'pontuacao'
));

$router->add('/empresa/pontuacao/pesquisar-pontuacao', array(
    'module' => 'empresa',
    'controller' => 'empresa_pontuacao',
    'action' => 'pesquisarPontuacao'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/pontuacao/modal-pontuacao/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa_pontuacao',
    'action' => 'modalPontuacao'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/empresa/pontuacao/salvar-pontuacao', array(
    'module' => 'empresa',
    'controller' => 'empresa_pontuacao',
    'action' => 'salvarPontuacao'
));

$router->add('/empresa/pontuacao/ativar-inativar-pontuacao/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'empresa_pontuacao',
    'action' => 'ativarInativarPontuacao'
));
/******Rotas módulo empresa/colaborador**********/

$router->add('/empresa/colaborador', array(
    'module' => 'empresa',
    'controller' => 'empresa_colaborador',
    'action' => 'colaborador'
));

$router->add('/empresa/colaborador/pesquisar-colaborador', array(
    'module' => 'empresa',
    'controller' => 'empresa_colaborador',
    'action' => 'pesquisarColaborador'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/colaborador/modal-colaborador/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa_colaborador',
    'action' => 'modalColaborador'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/empresa/colaborador/salvar-colaborador', array(
    'module' => 'empresa',
    'controller' => 'empresa_colaborador',
    'action' => 'salvarColaborador'
));

$router->add('/empresa/colaborador/ativar-inativar-colaborador/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'empresa_colaborador',
    'action' => 'ativarInativarColaborador'
));


/******Rotas módulo colaborador**********/

$router->add('/colaborador', array(
    'module' => 'colaborador',
    'controller' => 'colaborador',
    'action' => 'index'
));

$router->add('/colaborador/modal-desafios/{code}', array(
    'module' => 'colaborador',
    'controller' => 'desafio',
    'action' => 'modalDesafios'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/responder-desafio', array(
    'module' => 'colaborador',
    'controller' => 'desafio',
    'action' => 'responderDesafio'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});
$router->add('/colaborador/desafio-cumprido', array(
    'module' => 'colaborador',
    'controller' => 'desafio',
    'action' => 'desafioCumprido'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/modal-ajudas/{code}', array(
    'module' => 'colaborador',
    'controller' => 'ajuda',
    'action' => 'modalAjudas'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/ajuda/pedir-ajuda', array(
    'module' => 'colaborador',
    'controller' => 'ajuda',
    'action' => 'pedirAjuda'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/ajuda/excluir-ajuda', array(
    'module' => 'colaborador',
    'controller' => 'ajuda',
    'action' => 'excluirAjuda'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/modal-ajudar/{ajudaId}', array(
    'module' => 'colaborador',
    'controller' => 'ajuda',
    'action' => 'modalAjudar'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/ajudar', array(
    'module' => 'colaborador',
    'controller' => 'ajuda',
    'action' => 'ajudar'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/modal-noticias/{code}', array(
    'module' => 'colaborador',
    'controller' => 'noticia',
    'action' => 'modalNoticias'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/noticia/modal-ler-noticia/{code}', array(
    'module' => 'colaborador',
    'controller' => 'noticia',
    'action' => 'modalLerNoticia'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/modal-anotacoes/{code}', array(
    'module' => 'colaborador',
    'controller' => 'colaborador',
    'action' => 'modalAnotacoes'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/anotacao/salvar-anotacao', array(
    'module' => 'colaborador',
    'controller' => 'colaborador',
    'action' => 'salvarAnotacao'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/anotacao/excluir-anotacao', array(
    'module' => 'colaborador',
    'controller' => 'colaborador',
    'action' => 'excluirAnotacao'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/perfil', array(
    'module' => 'colaborador',
    'controller' => 'perfil',
    'action' => 'perfil'
));

$router->add('/empresa/ideia', array(
    'module' => 'empresa',
    'controller' => 'empresa_ideia',
    'action' => 'ideia'
));

$router->add('/empresa/ideia/pesquisar-ideia/filter/{get}', array(
    'module' => 'empresa',
    'controller' => 'empresa_ideia',
    'action' => 'pesquisarIdeia',
    'get' => 1
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/ideia/pesquisar-ideia', array(
    'module' => 'empresa',
    'controller' => 'empresa_ideia',
    'action' => 'pesquisarIdeia'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/ideia/modal-ideia/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa_ideia',
    'action' => 'modalIdeia'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/empresa/ideia/guardar-aprovar', array(
    'module' => 'empresa',
    'controller' => 'empresa_ideia',
    'action' => 'guardarAprovarIdeia'
));

$router->add('/empresa/ideia/modal-mapear-pontuacao', array(
    'module' => 'empresa',
    'controller' => 'empresa_ideia',
    'action' => 'mapearPontuacao'
));

$router->add('/empresa/perfil', array(
    'module' => 'empresa',
    'controller' => 'empresa_perfil',
    'action' => 'perfil'
));

$router->add('/colaborador/ideia', array(
    'module' => 'colaborador',
    'controller' => 'ideia',
    'action' => 'ideia'
));

$router->add('/colaborador/ideia/modal-ideias/{code}', array(
    'module' => 'colaborador',
    'controller' => 'ideia',
    'action' => 'modalIdeias'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/ideia/salvar-ideia', array(
    'module' => 'colaborador',
    'controller' => 'ideia',
    'action' => 'salvarIdeia'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/colaborador/ideia/excluir-ideia', array(
    'module' => 'colaborador',
    'controller' => 'ideia',
    'action' => 'excluirIdeia'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

//$router->add('/colaborador/metas/{tipo}', array(
//    'module' => 'colaborador',
//    'controller' => 'meta',
//    'action' => 'meta'
//));

$router->add('/colaborador/chat', array(
    'module' => 'colaborador',
    'controller' => 'chat',
    'action' => 'chat'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});


return $router;