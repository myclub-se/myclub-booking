<?php return array(
    'root' => array(
        'name' => 'myclub/myclub-booking',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => 'e72ddc84240c76498974ed4033cc337b62208524',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'myclub/common-lib' => array(
            'pretty_version' => '1.0.3',
            'version' => '1.0.3.0',
            'reference' => '305e4ef73395b6f6354229cc1571ffeb935e35bb',
            'type' => 'library',
            'install_path' => __DIR__ . '/../myclub/common-lib',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'myclub/myclub-booking' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'e72ddc84240c76498974ed4033cc337b62208524',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
