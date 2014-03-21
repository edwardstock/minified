<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
	'name'=>'MINIFIED.pw',
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
	'preload'=>['log','debug'],

	'language' => 'ru_RU',
	'sourceLanguage' => 'en_US',

    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],

	    'urlManager'=>[
		    'class'=>'yii\web\UrlManager',
		    'enablePrettyUrl'=>true,
	    ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace', 'info', 'error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

	    'i18n'=>[
		    'translations' => [
			    'app*'=>[
				    'class' => 'yii\i18n\PhpMessageSource',
				    'basePath' => "@app/messages",
				    'sourceLanguage' => 'en_US',
				    'fileMap' => [
					    'user'=>'user.php',
				    ]
			    ],
			    'yii'=>[
				    'class' => 'yii\i18n\PhpMessageSource',
				    'basePath' => "@app/messages",
				    'sourceLanguage' => 'en_US',
				    'fileMap' => [
					    'yii'=>'yii.php',
				    ]
			    ]
		    ]
	    ],
    ],

	'modules'=>[
		'user' => [
			'class' => 'frontend\modules\user\Module',
		],
		'storage'=>[
			'class' => 'frontend\modules\storage\Module',
		],

		'debug'=>[
			'class'=>'yii\debug\Module',
			'allowedIPs'=>['*','::1'],
		],

		'gii'=>[
			'class'=>'yii\gii\Module',
			'allowedIPs'=>['*','::1'],
		],
	],
    'params' => $params,
];
