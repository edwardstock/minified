<?php
namespace frontend\modules\user\controllers;


use frontend\modules\user\models\User;
use yii\web\AccessControl;
use yii\web\Controller;
use Yii;


class AuthController extends Controller
{

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'actions' => ['signup', 'login'],
						'allow' => true,
						'roles' => ['?'],
					],
					[
						'actions'=>['logout'],
						'allow'=>true,
						'roles'=>['@'],
					],
				],
			],
		];
	}

	public function actionLogin() {
		if ( !\Yii::$app->user->isGuest ) {
			return $this->goHome();
		}
		$model = new User();
		$model->setScenario('login');
		if ( $model->load(Yii::$app->request->post()) && $model->getUser() !== null && $model->login() ) {
			return $this->goBack();
		} else {
			return $this->render('login', [
				'model' => $model,
			]);
		}
	}

	public function actionSignup() {
		if ( !\Yii::$app->user->isGuest ) {
			return $this->goHome();
		}


		$model = new User();
		$model->setScenario('registering');

		if ( \Yii::$app->request->isPost && $model->load($_POST) && $model->validate() ) {
			$model->save();
			$model->generatePersonalToken();
			$model->sendRegisteredEmail();
			\Yii::$app->session->setFlash('success',
				'You are successfully registered! Check your email for activating link.');

			$this->redirect(\Yii::$app->getUrlManager()->createUrl(['/user/auth/login']));
		}

		return $this->render('signup', [
			'model' => $model
		]);
	}

	public function actionLogout() {
		\Yii::$app->user->logout(true);
		$this->goBack();
	}
} 