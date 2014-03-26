<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

NavBar::begin([
	'brandLabel' => \yii\helpers\Html::img('/images/minifiedlogo.png',['style'=>'height: 72px;box-shadow: 5px 4px 0px #000;position: relative;
right: 5px;']),
	'brandUrl' => '/',
	'options' => [
		'class' => 'navbar-inverse navbar-fixed-top',
	],
]);
$menuItems = [
	['label' => 'Home', 'url' => ['/site/index']],
	['label' => 'About', 'url' => ['/site/about']],
	['label'=>'FAQ','url'=>['/site/faq']],
	['label' => 'Bug me baby', 'url' => ['/site/contact']],
];
if (Yii::$app->user->isGuest) {
	$menuItems[] = ['label' => 'Signup', 'url' => ['/user/auth/signup']];
	$menuItems[] = ['label' => 'Login', 'url' => ['/user/auth/login']];
} else {
	$menuItems[] = [
		'label'=>'Profile',
		'url'=>['/user/profile/index'],
	];
	$menuItems[] = [
		'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
		'url' => ['/user/auth/logout'],
		'linkOptions' => ['data-method' => 'post'],
		'icon'=>'user'
	];

}
echo Nav::widget([
	'options' => ['class' => 'navbar-nav navbar-right'],
	'items' => $menuItems,
]);
NavBar::end();