<?php
/*
 * Defini rotas personalizadas. Arquivo é incluído na definição do serviço roteador.
 */
$router = new Phalcon\Mvc\Router(false);

$router->setDefaultModule("publico");

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
    'controller' => 'empresa',
    'action' => 'index'
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

$router->add('/empresa/desafio/ativar-inativar-desafio/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'empresa_desafio',
    'action' => 'ativarInativarDesafio'
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

return $router;