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
    'controller' => 'desafio',
    'action' => 'desafio'
));

$router->add('/empresa/desafio/pesquisar-desafio', array(
    'module' => 'empresa',
    'controller' => 'desafio',
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
    'controller' => 'desafio',
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
    'controller' => 'desafio',
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
    'controller' => 'desafio',
    'action' => 'salvarDesafio'
));

$router->add('/empresa/desafio/ativar-inativar-desafio/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'desafio',
    'action' => 'ativarInativarDesafio'
));

/******Rotas módulo empresa/regra**********/

$router->add('/empresa/regra', array(
    'module' => 'empresa',
    'controller' => 'regra',
    'action' => 'regra'
));

$router->add('/empresa/regra/pesquisar-regra', array(
    'module' => 'empresa',
    'controller' => 'regra',
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
    'controller' => 'regra',
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
    'controller' => 'regra',
    'action' => 'salvarRegra'
));

$router->add('/empresa/regra/ativar-inativar-regra/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'regra',
    'action' => 'ativarInativarRegra'
));
/******Rotas módulo empresa/pontuacao**********/

$router->add('/empresa/pontuacao', array(
    'module' => 'empresa',
    'controller' => 'pontuacao',
    'action' => 'pontuacao'
));

$router->add('/empresa/pontuacao/pesquisar-pontuacao', array(
    'module' => 'empresa',
    'controller' => 'pontuacao',
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
    'controller' => 'pontuacao',
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
    'controller' => 'pontuacao',
    'action' => 'salvarPontuacao'
));

$router->add('/empresa/pontuacao/ativar-inativar-pontuacao/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'pontuacao',
    'action' => 'ativarInativarPontuacao'
));
/******Rotas módulo empresa/colaborador**********/

$router->add('/empresa/colaborador', array(
    'module' => 'empresa',
    'controller' => 'colaborador',
    'action' => 'colaborador'
));

$router->add('/empresa/colaborador/pesquisar-colaborador', array(
    'module' => 'empresa',
    'controller' => 'colaborador',
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
    'controller' => 'colaborador',
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
    'controller' => 'colaborador',
    'action' => 'salvarColaborador'
));

$router->add('/empresa/colaborador/ativar-inativar-colaborador/{status}/{id}', array(
    'module' => 'empresa',
    'controller' => 'colaborador',
    'action' => 'ativarInativarColaborador'
));


/******Rotas módulo coloborador**********/

$router->add('/colaborador', array(
    'module' => 'colaborador',
    'controller' => 'colaborador',
    'action' => 'index'
));

return $router;