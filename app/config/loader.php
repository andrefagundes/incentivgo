<?php

$loader = new \Phalcon\Loader();

/**
 * Registra um conjunto de diretórios retirados do arquivo de configuração,
 * aqui somente os diretórios comuns para todos os módulos
 */
$loader->registerNamespaces(array(
    'Incentiv\Models' => __DIR__ .$config->application->modelsDir,
    'Incentiv' => __DIR__ .$config->application->libraryDir,
    'Incentiv\Plugins' => __DIR__ .$config->application->pluginsDir
))->register();