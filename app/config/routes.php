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

/*************fim************/

/******Rotas módulo aluno**********/

$router->add('/aluno/usuario/alteraSenha', array(
    'module' => 'aluno',
    'controller' => 'usuario',
    'action' => 'alteraSenha'
));

/*************fim************/

return $router;
