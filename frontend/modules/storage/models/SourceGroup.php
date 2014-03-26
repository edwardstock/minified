<?php

namespace frontend\modules\storage\models;
use common\helpers\ES;
use frontend\modules\storage\models\Source;
use frontend\modules\user\models\User;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "storage_group".
 *
 * @property string $id
 * @property string $userId
 * @property string $title
 * @property string $description
 *
 * @property Source[] $storages
 */
class SourceGroup extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'source_group';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['userId', 'title'], 'required'],
			[['userId'], 'integer'],
			[['description'], 'string'],
			[['title'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'userId' => 'User ID',
			'title' => 'Title',
			'description' => 'Description',
		];
	}

	public function beforeValidate() {
		$this->userId = \Yii::$app->user->identity->getId();

		return parent::beforeValidate();
	}

	/**
	 * @param $userId
	 * @return ActiveDataProvider
	 */
	public static function findAllByUser($userId) {
		return new ActiveDataProvider([
			'query'=>static::find()
					->where(['userId'=>$userId])
					//->with('storages')
		]);
	}

	public static function findByUser($userId) {
		return static::find(['userId'=>$userId])->one();
	}


	public function getUser() {
		return $this->hasOne(User::class, ['userId'=>'id']);
	}

	public function getStorages() {
		return $this->hasMany(Source::class, ['groupId'=>'id']);
	}
}
