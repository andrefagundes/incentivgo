<?php 
return new \Phalcon\Config(array(
    'database' => array(
        'adapter'   => 'Mysql',
        'host'      => 'localhost',
        'port'      => '52303',
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
//        'baseUrl'        => '/incentiv/',
//        'publicUrl'      => 'localhost/incentiv',
        'baseUrl'        => '/',
        'publicUrl'      => $_SERVER['HTTP_HOST'],
        'cryptSalt'      => 'G-KY^vSK@:(jW_+gvLU:HeRVi!ZK(KV{bDp=T%l.oGaWZ?mjht<N#7 _E#2]O_8^'
    ),
    'mail' => array(
        'fromEmail' => 'amfcom@gmail.com',
        'smtp' => array(
            'server'    => 'smtp.sendgrid.net',
            'port'      => 465,
            'security'  => 'ssl',
            'username'  => 'amfcom',
            'password'  => 'mfcom5841'
        )
    ),
    'amazon' => array(
        'AWSAccessKeyId'    => '',
        'AWSSecretKey'      => ''
    )
));