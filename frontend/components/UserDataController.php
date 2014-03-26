<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: UserDataController
 */
namespace frontend\components;

use yii\web\AccessControl;
use yii\web\Controller;
use yii\web\VerbFilter;

class UserDataController extends Controller {

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'actions' => ['signup','login',],
						'allow' => false,
						'roles' => ['?'],
					],
					[
						'allow'=>true,
						'roles'=>['@'],
					],
				],
			],
		];
	}

	public function createUrl($action) {
		$moduleName = $this->module->id;
		$controllerName = strtolower(str_replace('Controller','',$this->id));
		return \Yii::$app->getUrlManager()->createUrl(['/'.$controllerName.'/']);
	}
} 