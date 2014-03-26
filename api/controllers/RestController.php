<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: RestController
 */

namespace api\controllers;


use api\components\ApiController;
use common\helpers\ES;
use common\helpers\Html;
use frontend\modules\user\models\User;
use yii\web\BadRequestHttpException;
use yii\web\VerbFilter;

class RestController extends ApiController {

	public function behaviors() {
		return [
//			'access' => [
//				'class' => AccessControl::class,
//				'only' => ['logout', 'signup'],
//				'rules' => [
//					[
//						'actions' => ['signup'],
//						'allow' => true,
//						'roles' => ['?'],
//					],
//					[
//						'actions' => ['logout'],
//						'allow' => true,
//						'roles' => ['@'],
//					],
//				],
//			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'auth' => ['post'],
				],
			],
		];
	}

	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	public function actionIndex() {

	}

	public function actionAuth() {
		if(!isset($_POST['username']) || !isset($_POST['token']))
			throw new BadRequestHttpException("Required params 'username' and 'token' are not exists");

		$username = (string) Html::encode($_POST['username']);
		$token = (string) Html::encode($_POST['token']);

		$user = User::findByUsername($username);

		if($user === null) {
			echo $this->setJsonResponse([
				'error'=>self::API_ERROR_USER_NOT_FOUND,
				'data'=>[
					'username'=>$username,
					'token'=>$token
				],
			]);
			return;
		}

		if($user->token !== $token) {
			echo $this->setJsonResponse([
				'error'=>self::API_ERROR_USER_TOKEN_DOES_NOT_MATCH,
				'data'=>[
					'username'=>$username,
					'token'=>$token
				],
			]);
			return;
		}

		echo $this->setJsonResponse([
			'error'=>self::API_NO_ERRORS,
			'data'=>[
				'username'=>$user->username
			],
		]);

	}

} 