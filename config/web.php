<?php
$config = [
    'id' => 'basic',
	'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'authManager' => [
            'class' => 'app\components\DbManager',
	        'defaultRoles' => ['guest'],
        ],
        'request' => [
            'cookieValidationKey' => 'Z893Dzd8FORCYKDK-tGM-vgzrSqXHTR7',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'index/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ''           => 'index/index',
                'login'      => 'index/login',
                'logout'     => 'index/logout',
                'download'   => 'index/download',
	            'users'      => 'user/index',
	            'objects'    => 'objects/index',
            ],
        ],
    ],
	'modules' => [
		'gridview' => [
			'class' => '\kartik\grid\Module',
		],
		'feedback' => [
			'class' => 'app\modules\feedback\Feedback',
		],
	],
    'params' => require(__DIR__ . '/params.php'),
];

if (isset($_COOKIE['dev'])) {
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		'allowedIPs' => [
			'212.224.118.66',
			'127.0.0.1',
			'*',
		]
	];

	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
