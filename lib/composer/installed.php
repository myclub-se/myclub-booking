<?php return array(
    'root' => array(
        'name' => 'myclub/myclub-booking',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => '704ac931d7d98d919489f145933e6472268d5f69',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'myclub/common-lib' => array(
            'pretty_version' => '1.0',
            'version' => '1.0.0.0',
            'reference' => '94a1ff9f30c159bab7222be26a6c4108a1f65abb',
            'type' => 'library',
            'install_path' => __DIR__ . '/../myclub/common-lib',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'myclub/myclub-booking' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => '704ac931d7d98d919489f145933e6472268d5f69',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
