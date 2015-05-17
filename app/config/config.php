<?php
return new \Phalcon\Config(array(
    'database' => array(
        'adapter'   => 'Mysql',
        'host'      => 'localhost',
        'username'  => 'andremfagundes',
        'password'  => '98^L3yF `x:3IJ4i}Kj=bGhx-+,.`3lc-(uy,rMQ1Ad <&JI2>:TuWE8{jy)+y|+',
        'dbname'    => 'incentiv_amf'
    ),
    'application' => array(
        'modelsDir'      => '/../../app/models/',
        'cacheDir'       => '/../../app/cache/',
        'libraryDir'     => '/../../app/library/',
        'pluginsDir'     => '/../../app/plugins/',
        'baseImage'      => __DIR__ . '/../../public/img/',
        'baseUrl'        => '/incentiv/',
        'publicUrl'      => '/',
        'cryptSalt'      => 'G-KY^vSK@:(jW_+gvLU:HeRVi!ZK(KV{bDp=T%l.oGaWZ?mjht<N#7 _E#2]O_8^'
    ),
    'mail' => array(
        'fromName'  => 'Incentiv Go',
        'fromEmail' => 'amfcom@gmail.com',
        'smtp' => array(
            'server'    => 'smtp.gmail.com',
            'port'      => 465,
            'security'  => 'ssl',
            'username'  => 'amfcom@gmail.com',
            'password'  => 'wivwgjkkvcksombb'
        )
    ),
    'amazon' => array(
        'AWSAccessKeyId'    => '',
        'AWSSecretKey'      => ''
    )
));