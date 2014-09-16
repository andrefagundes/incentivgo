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
    'empresa' => array(
        'className' => 'Empresa\Module',
        'path' => __DIR__ . '/../modules/empresa/Module.php'
    ),
    'colaborador' => array(
        'className' => 'Colaborador\Module',
        'path' => __DIR__ . '/../modules/colaborador/Module.php'
    )
));