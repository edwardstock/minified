<?php
/**
 * Created by PhpStorm.
 * User: edwardstock
 * Date: 21.03.14
 * Time: 12:28
 */

namespace frontend\modules\user\controllers;


use frontend\modules\user\models\User;
use yii\web\Controller;
use Yii;


class AuthController extends Controller {

	public function actionLogin() {
		if (!\Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new User('login');
		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			return $this->goBack();
		} else {
			return $this->render('login', [
				'model' => $model,
			]);
		}
	}

	public function actionSignup() {
		if(!\Yii::$app->user->isGuest)
			return $this->goHome();


		$model = new User('registering');

		if($model->load(Yii::$app->request->post()) && $model->validate()) {
			$model->save();
			$model->sendRegisteredEmail();


		}
	}
} 