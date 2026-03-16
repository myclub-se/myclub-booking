<?php return array(
    'root' => array(
        'name' => 'myclub/myclub-booking',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => 'f3383c5731cdce8834201e93f7aaab7b7d9985f5',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'myclub/common-lib' => array(
            'pretty_version' => '1.0',
            'version' => '1.0.0.0',
            'reference' => 'f7b623cba69b2b3fbb3d344f94bb11403c9556bc',
            'type' => 'library',
            'install_path' => __DIR__ . '/../myclub/common-lib',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'myclub/myclub-booking' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'f3383c5731cdce8834201e93f7aaab7b7d9985f5',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
