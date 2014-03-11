<?php

$loader = new \Phalcon\Loader();

/**
 * Registra um conjunto de diretórios retirados do arquivo de configuração
 */
$loader->registerNamespaces(array(
    'Incentiv\Models' => __DIR__ .$config->application->modelsDir,
    'Incentiv\Controllers' => __DIR__ .$config->application->controllersDir,
    'Incentiv\Forms' => __DIR__ .$config->application->formsDir,
    'Incentiv' => __DIR__ .$config->application->libraryDir
)
)->register();
