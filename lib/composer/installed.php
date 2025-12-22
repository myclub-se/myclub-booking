<?php return array(
    'root' => array(
        'name' => 'myclub/myclub-booking',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => '29ea833e19f3ddff1d32b9e4d994dc57bd3d8f51',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'myclub/common-lib' => array(
            'pretty_version' => '1.0',
            'version' => '1.0.0.0',
            'reference' => '5206c1f4c9bc22f1e672d647735295a3e4796b68',
            'type' => 'library',
            'install_path' => __DIR__ . '/../myclub/common-lib',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'myclub/myclub-booking' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => '29ea833e19f3ddff1d32b9e4d994dc57bd3d8f51',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
