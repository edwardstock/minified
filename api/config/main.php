<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
	'name'=>'MINIFIED.pw',
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
	'preload'=>['log','debug'],

	'language' => 'en_US',
	'sourceLanguage' => 'en_US',

    'components' => [
        'user' => [
            'identityClass' => frontend\modules\user\models\User::class,
            'enableAutoLogin' => true,
	        'loginUrl'=>['/user/auth/login'],
        ],

	    'urlManager'=>[
		    'class'=>\yii\web\UrlManager::class,
		    'enablePrettyUrl'=>true,
		    'rules'=>[
			    'user/auth'=>'rest/auth'
		    ],
	    ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['trace', 'info', 'error', 'warning'],
                ],
	            [
		            'class' => \yii\log\DbTarget::class,
		            'levels' => ['warning','error'],
	            ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'rest/error',
        ],

	    'i18n'=>[
		    'translations' => [
			    'buttons'=>[
				    'class' => \yii\i18n\PhpMessageSource::class,
				    'basePath' => "@app/messages",
				    'sourceLanguage' => 'ru_RU',
				    'fileMap' => [
					    'user'=>'user.php',
					    'buttons'=>'buttons.php'
				    ]
			    ],
//
		    ]
	    ],
    ],

	'modules'=>[
//		'user' => [
//			'class' => \frontend\modules\user\Module::class,
//		],
//		'storage'=>[
//			'class' => \frontend\modules\storage\Module::class,
//		],

		'debug'=>[
			'class'=>\yii\debug\Module::class,
			'allowedIPs'=>['*','::1'],
		],

		'gii'=>[
			'class'=>\yii\gii\Module::class,
			'allowedIPs'=>['*','::1'],
		],
	],
    'params' => $params,
];
