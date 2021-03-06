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

	'language' => 'en_US',
	'sourceLanguage' => 'en_US',

    'components' => [

	    'minified'=> [
		    'class'=> EdwardStock\Minified\MinifiedClient::class,
		    'username'=>'admin',
		    'token'=>'21232f297a57a5a743894a0e4a801fc3',
		    'jsCompilationLevel'=>EdwardStock\Minified\MinifiedClient::COMPILATION_LEVEL_SIMPLE_OPTIMIZATION,
		    'jsSpecification'=>EdwardStock\Minified\MinifiedClient::SPEC_DEFAULT_ECMASCRIPT3,
		    'yiiDebug'=>YII_DEBUG,
		    'combineCss'=>false,
		    'combineJs'=>false,
		    'recursiveJsScan'=>true,
		    'recursiveCssScan'=>true,
		    'assetsDepends'=> [
				//for example like this
			    yii\web\YiiAsset::class,
			    yii\bootstrap\BootstrapAsset::class,
		    ],
		    'sourceJsPaths'=>[
			    //your js path
			    \Yii::getAlias('@frontend/web/js')
		    ],
		    'sourceCssPaths'=>[
			    //your css path
			    \Yii::getAlias('@frontend/web/css')
		    ],
	    ],

        'user' => [
            'identityClass' => frontend\modules\user\models\User::class,
            'enableAutoLogin' => true,
	        'loginUrl'=>['/user/auth/login'],
        ],

	    'urlManager'=>[
		    'class'=>yii\web\UrlManager::class,
		    'enablePrettyUrl'=>true,
	    ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['trace', 'info', 'error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
		'user' => [
			'class' => frontend\modules\user\Module::class,
		],
		'storage'=>[
			'class' => frontend\modules\storage\Module::class,
		],

		'debug'=>[
			'class'=>yii\debug\Module::class,
			'allowedIPs'=>['*','::1'],
		],

		'gii'=>[
			'class'=>yii\gii\Module::class,
			'allowedIPs'=>['*','::1'],
		],
	],
    'params' => $params,
];
