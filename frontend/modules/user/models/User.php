<?php

namespace frontend\modules\user\models;

use common\helpers\DateTime;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Security;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $token
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $authKey
 * @property bool $active
 * @property string $registeredAt
 * @property string $loginAt
 */
class User extends ActiveRecord implements IdentityInterface
{
	public $rememberMe = false;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'user';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['email'],'email'],
			[['email','username'], 'unique', 'on'=>'registering'],
			[['username', 'password', 'email', 'authKey','registeredAt'], 'required', 'on'=>'registering'],
			[['loginAt'], 'required', 'on'=>'login'],
			[['active'], 'integer'],
			[['username', 'password', 'email'], 'string', 'max' => 255],
			[['token'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'authKey' => 'Auth key',
		];
	}

	public function beforeValidate() {
		if($this->scenario === 'login') {
			$this->loginAt = DateTime::getDateTime();
		}

		if($this->isNewRecord || $this->scenario === 'registering') {
			$this->registeredAt = DateTime::getDateTime();
			$this->authKey = password_hash($this->id . $this->username, PASSWORD_BCRYPT);
		}

		return parent::beforeValidate();
	}

	public function beforeSave($insert) {
		if($this->isNewRecord) {
			$this->password = password_hash($this->password, PASSWORD_BCRYPT);
		}

		return parent::beforeSave($insert);
	}


	public function afterFind() {
		if($this->token === null) {
			$this->generatePersonalToken();
		}

		parent::afterFind();
	}

	/**
	 * Finds an identity by the given ID.
	 * @param  string|integer $id the ID to be looked for
	 * @return IdentityInterface the identity object that matches the given ID.
	 *                              Null should be returned if such an identity cannot be found
	 *                              or the identity is not in an active state (disabled, deleted, etc.)
	 */
	public static function findIdentity($id) {
		return static::find($id);
	}

	/**
	 * Finds an identity by the given secrete token.
	 * @param  string $token the secrete token
	 * @return IdentityInterface the identity object that matches the given token.
	 *                                 Null should be returned if such an identity cannot be found
	 *                                 or the identity is not in an active state (disabled, deleted, etc.)
	 */
	public static function findIdentityByAccessToken($token) {
		return static::find(['authKey' => $token]);
	}

	/**
	 * Returns an ID that can uniquely identify a user identity.
	 * @return string|integer an ID that uniquely identifies a user identity.
	 */
	public function getId() {
		return $this->getPrimaryKey();
	}

	/**
	 * Returns a key that can be used to check the validity of a given identity ID.
	 *
	 * The key should be unique for each individual user, and should be persistent
	 * so that it can be used to check the validity of the user identity.
	 *
	 * The space of such keys should be big enough to defeat potential identity attacks.
	 *
	 * This is required if [[User::enableAutoLogin]] is enabled.
	 * @return string a key that is used to check the validity of a given identity ID.
	 * @see validateAuthKey()
	 */
	public function getAuthKey() {
		return $this->authKey;
	}

	/**
	 * Generates unique auth key
	 */
	public function generateAuthKey() {
		$this->authKey = Security::generateRandomKey(64);
	}

	/**
	 * Validates the given auth key.
	 *
	 * This is required if [[User::enableAutoLogin]] is enabled.
	 * @param string $authKey the given auth key
	 * @return boolean whether the given auth key is valid.
	 * @see getAuthKey()
	 */
	public function validateAuthKey($authKey) {
		return password_verify($this->id . $this->username, $this->authKey);
	}

	public function validatePassword($password) {
		return password_verify($password, $this->password);
	}

	public function login() {
		if ( $this->validate() ) {
			$user = $this->getUser();
			if($user === null) {
				$this->addError('username','User does not exists. Please check your input');
				return false;
			}

			if($user->active == 0) {
				$this->addError('active', 'Your account is not activated. Please check your email or get new activating message');
				$this->sendRegisteredEmail();
				return false;
			}
			$user->generatePersonalToken();
			return \Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 7 : 0);
		}

		return false;
	}

	public function getUser() {
		/** @var User $user */
		$user = static::findByUsername($this->username);
		if($user === null) {
			$this->addError('username','User does not exists');
			return null;
		}
		if($user->validatePassword($this->password))
			return $user;

		$this->addError('password','Wrong password');

		return null;
	}

	/**
	 * @param $username
	 * @return User
	 */
	public static function findByUsername($username) {
		return static::find(['username'=>$username]);
	}

	/**
	 * @todo не отправляются письма
	 * @return bool
	 */
	public function sendRegisteredEmail() {
		return \Yii::$app->mail
			->compose('registeredUser', ['user' => $this])
			->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
			->setTo($this->email)
			->setSubject('Thanks for registering on '.\Yii::$app->name)
			->send();
	}

	/**
	 * @return string
	 */
	public function getActivationLink() {
		return Html::a("Activate Me", \Yii::$app->getUrlManager()->createAbsoluteUrl(['user/auth/activate','token'=>$this->authKey]));
	}

	public function generatePersonalToken() {
		if($this->token !== null && $this->token !== '')
			return false;

		$this->token = md5($this->username);
		return $this->save(false);
	}

}
