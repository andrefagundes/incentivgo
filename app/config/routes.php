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

/******Rotas módulo colaborador**********/

$router->add('/colaborador/usuario/alteraSenha', array(
    'module' => 'colaborador',
    'controller' => 'usuario',
    'action' => 'alteraSenha'
));

/******Rotas módulo empresa**********/

$router->add('/empresa', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'index'
));

$router->add('/empresa/campanhas', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'campanhas'
));

$router->add('/empresa/desafios', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'desafios'
));

$router->add('/empresa/pesquisar-desafios', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'pesquisarDesafios'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/modal-desafio/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'modalDesafio'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }

    return false;
});

$router->add('/empresa/salvar-desafio', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'salvarDesafio'
));

$router->add('/empresa/colaboradores', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'colaboradores'
));

$router->add('/empresa/pesquisar-colaboradores', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'pesquisarColaboradores'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/pesquisar-colaboradores/filter/{get}', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'pesquisarColaboradoresDesafio',
    'get' => 1
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/campanhas', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'campanhas'
));

$router->add('/empresa/criar-campanha', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'criarCampanha'
));

$router->add('/empresa/pesquisar-campanhas', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'pesquisarCampanhas'
))->beforeMatch(function() {
    //Verifica se a requisição é Ajax
    if ($_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
        return true;
    }
    return false;
});

$router->add('/empresa/eventos/{code}', array(
    'module' => 'empresa',
    'controller' => 'empresa',
    'action' => 'eventos'
));

return $router;