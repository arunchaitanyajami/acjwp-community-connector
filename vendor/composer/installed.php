<?php return array(
    'root' => array(
        'name' => 'acj/wp-community-connector',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => '96c33ee51c43f1e6f7c4bf7395247b6a7c0dc3aa',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'acj/wp-community-connector' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => '96c33ee51c43f1e6f7c4bf7395247b6a7c0dc3aa',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
    ),
);
