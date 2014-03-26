<?php
/**
 * @project minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: UserBehavior
 */

namespace common\behaviors;


use yii\base\Behavior;
use yii\db\ActiveRecord;

class UserBehavior extends Behavior {

	public $userAttribute = 'userId';

	public function events(){
		return [
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'insertUserId',
		];
	}

	public function insertUserId() {
		/** @var ActiveRecord $model */
		$model = $this->owner;

		if($model->isNewRecord && \Yii::$app->user->isGuest === false) {
			$model->{$this->userAttribute} = \Yii::$app->user->identity->getId();
		}
	}
}