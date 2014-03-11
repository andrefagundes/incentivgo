<?php
/**
 * Register application modules
 */

$application->registerModules(array(
    'publico' => array(
        'className' => 'Publico\Module',
        'path' => __DIR__ . '/../modules/publico/Module.php'
    ), 
    'admin' => array(
        'className' => 'Admin\Module',
        'path' => __DIR__ . '/../modules/admin/Module.php'
    ), 
    'instituicao' => array(
        'className' => 'Instituicao\Module',
        'path' => __DIR__ . '/../modules/instituicao/Module.php'
    ),
    'aluno' => array(
        'className' => 'Aluno\Module',
        'path' => __DIR__ . '/../modules/aluno/Module.php'
    )
));