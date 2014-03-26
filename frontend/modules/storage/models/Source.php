<?php

namespace frontend\modules\storage\models;

use common\helpers\DateTime;
use common\helpers\ES;
use common\helpers\HTTP;
use frontend\modules\storage\components\GoogleClosure;
use frontend\modules\storage\scopes\SourceQuery;
use frontend\modules\user\models\User;
use stdClass;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\mssql\PDO;
use yii\helpers\Json;

class StorageException extends Exception
{
	public function __construct($message, $code = 0) {
		parent::__construct($message, [], $code);
	}
}

/**
 * This is the model class for table "source".
 *
 * @property string $id
 * @property string $userId
 * @property string $publicToken
 * @property string $title
 * @property string $type
 * @property string $url
 * @property string $sourceData
 * @property string $targetData
 * @property integer $groupId
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $version
 * @property string $settings
 *
 * @method SourceQuery|static lastVersions()
 */
class Source extends ActiveRecord
{

	const TYPE_CSS = 'CSS';
	const TYPE_JS = 'JS';
	const CHARSET_UTF8 = 'UTF-8';
	const CHARSET_CP1251 = 'CP1251';

	public $updateVersion = false;
	public $updateMajorVersion = false;
	public $config = array(
		'obfuscateJs' => 1,
		'charset' => self::CHARSET_UTF8,
		'preserveSemicolons' => 0,
		'disableMicroOptimizations' => 0,
	);
	private $oldSourceData;

	public static function getTypes() {
		return array(
			'-- Please select type --',
			self::TYPE_CSS => 'CSS',
			self::TYPE_JS => 'JavaScript',
		);
	}

	public static function getCharsetList() {
		return array(
			self::CHARSET_UTF8 => 'UTF-8 (recommended)',
			self::CHARSET_CP1251 => 'Windows 1251'
		);
	}

	public static function booleanValues() {
		return array(
			1 => 'Yes',
			0 => 'No'
		);
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'source';
	}

	public static function getList() {
		return new ActiveDataProvider([
			'query' => Source::find()
					->with('group')
					->lastVersions()
		]);
	}

	public static function findByRest($token, $version) {
		return static::find(['publicToken' => $token, 'version' => $version]);
	}

	public static function createQuery($config = []) {
		$config['modelClass'] = get_called_class();
		return new SourceQuery($config);
	}


	public function behaviors() {
		return [
			'user' => [
				'class' => 'common\behaviors\UserBehavior'
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['userId', 'sourceData', 'groupId', 'type'], 'required'],
			[['userId', 'groupId'], 'integer'],
			[['publicToken','privateToken', 'type', 'url'], 'string'],
			[['sourceData', 'targetData'], 'string'],
			[['createdAt', 'updatedAt'], 'safe'],
			[['title'], 'string', 'max' => 255],
			[['settings', 'type', 'version'], 'safe'],
			[['type'], 'validateSourceType'],
			[['updateVersion', 'updateMajorVersion'], 'boolean'],
		];
	}

	public function validateSourceType($value) {
		if ( $value == '0' ) {
			$this->addError('type', 'You must select type');
		}
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'userId' => 'User ID',
			'publicToken' => 'Public token',
			'privateToken'=>'Private token',
			'title' => 'Title',
			'url' => 'URL',
			'sourceData' => 'Source',
			'targetData' => 'Target Data',
			'groupId' => 'Group',
			'createdAt' => 'Created At',
			'updatedAt' => 'Updated At',
			'version' => 'Version',
			'settings' => 'Settings',
			'updateVersion' => 'Pick up new version?',
			'updateMajorVersion' => 'New version is major?',
		];
	}

	public function getGroup() {
		return $this->hasOne(SourceGroup::class, ['id' => 'groupId']);
	}

	public function getUser() {
		return $this->hasOne(User::class, ['userId' => 'id']);
	}

	public function beforeValidate() {
		if ( $this->isNewRecord ) {
			$this->createdAt = DateTime::getDateTime();
		}
		$this->updatedAt = DateTime::getDateTime();

		if ( !$this->isNewRecord && $this->oldSourceData != $this->sourceData )
			$this->processData();


		return parent::beforeValidate();
	}

	public function processData() {

		$googleClosure = new GoogleClosure($this->sourceData);
		$googleClosure->enableProfiling(true);
		$googleClosure->enableValidatingSource(true);
		$googleClosure->execute();
		$result = $googleClosure->getResult();
		//
		$debug = $googleClosure->getDebugInfo();
		$runtime = $googleClosure->getRuntimeOutput();


		if ( $googleClosure->hasErrors() ) {
			$this->addError('sourceData', $googleClosure->getErrors());
		} else if ( $googleClosure->hasWarnings() ) {
			$this->addError('sourceData', $googleClosure->getWarnings());
		}

		$this->targetData = $result;
	}

	public function beforeSave($insert) {
		if ( is_array($this->settings) && !empty($this->settings) ) {
			$this->settings = Json::encode($this->settings);
		}

		if ( $this->title === null || $this->title === '' ) {
			$this->title = $this->type . '-' . date('YmdHis') . '-' . md5($this->userId);
		}

		$this->createNewVersion();

		return parent::beforeSave($insert);
	}

	public function createNewVersion() {

		if ( $this->isNewRecord && !$this->updateVersion )
			return false;

		$oldVersion = (double)$this->version;
		if ( $this->updateMajorVersion ) {
			$newVersion = ($oldVersion & $oldVersion) + 1;
		} else {
			$newVersion = $oldVersion + 0.1;
		}

		$source = new Source();
		$source->publicToken = $this->publicToken;
		$source->userId = $this->userId;
		$source->groupId = $this->groupId;
		$source->title = $this->title;
		$source->type = $this->type;
		$source->url = $this->url;
		$source->sourceData = $this->sourceData;
		$source->targetData = $this->targetData;
		$source->createdAt = DateTime::getDateTime();
		$source->updatedAt = DateTime::getDateTime();
		$source->version = $newVersion;
		$source->settings = $this->settings;
		$source->save(false);

		return true;
	}

	public function afterFind() {
		if ( $this->settings !== null ) {
			$this->settings = Json::decode($this->settings);
		}

		$this->oldSourceData = $this->sourceData;

		parent::afterFind();
	}

	public function appendToken() {
		$this->publicToken = $this->generatePublicToken();

		return $this->save(false);
	}

	/**
	 * Hashed unique token
	 * @return string
	 */
	private function generatePublicToken() {
		return md5($this->id . $this->userId . $this->version);
	}

	public function compareVersions() {
		if ( $this->version == 1.0 || $this->version === null )
			return false;

		return true;
	}

	public function getRawUrl() {
		$query = array(
			'token' => $this->publicToken,
			'version' => $this->version,
		);

		return "http://api.minified.pw/source/get?" . HTTP::buildUrl($query);
	}

	public function getVersionList($token) {
		$sql = "SELECT `id`,`version` FROM `source` WHERE `publicToken` = :token";

		$command = $this->getDb()->createCommand($sql);
		$command->bindParam(':token', $token, PDO::PARAM_STR);
		/** @var stdClass $result */
		$result = $command->queryAll(PDO::FETCH_OBJ);

		$out = array();
		foreach ( $result AS $version ) {
			$out[$version->id] = $version->version;
		}

		return $out;
	}

}
