<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: ProfileController
 */

namespace frontend\modules\user\controllers;


use frontend\components\UserDataController;
use frontend\modules\storage\models\SourceGroup;
use frontend\modules\user\models\User;

class ProfileController extends UserDataController
{

	public function actionIndex() {

		$groups = SourceGroup::findAllByUser(\Yii::$app->user->id);


		return $this->render('index', [
			'groups' => $groups
		]);
	}

	public function actionGetToken() {
		/** @var User $user */
		$user = User::find(\Yii::$app->user->getId());
		return $this->render('get_token',[
			'token'=>$user->token
		]);
	}

	public function actionSettings() {

	}

	public function actionStorage() {

	}


} 