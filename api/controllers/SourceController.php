<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: SourceController
 */

namespace api\controllers;


use api\components\ApiController;
use common\helpers\ES;
use common\helpers\HTTP;
use frontend\modules\storage\components\GoogleClosure;
use frontend\modules\storage\models\Source;
use frontend\modules\user\models\User;
use yii\base\ErrorException;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\VerbFilter;

class SourceController extends ApiController
{

	public $defaultAction = 'get';

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
					'update' => ['put'],
					'add'=>['put'],
					'get'=>['get'],
					'get-static'=>['get'],
					'delete' => ['delete'],
				],
			],
		];
	}

	public function actionGet($token, $version) {
		$token = Html::encode($token);
		$version = (double)Html::encode($version);

		/** @var Source $source */
		$source = Source::findByRest($token, $version);

		if ( $source === null ) {
			throw new HttpException(404, 'Source not found');
		}

		$header = "Content-type: ";
		if ( $source->type === Source::TYPE_JS ) {
			$header .= 'text/javascript';
		} else if ( $source->type === Source::TYPE_CSS ) {
			$header .= 'text/css';
		} else {
			$header .= 'text/plain';
		}

		header('HTTP/1.0 200');
		header('Content-MD5: ' . base64_encode(md5($source->targetData)));
		header('Content-Version: ' . $source->version);
		header('Last-Modified: ' . $source->updatedAt);
		header($header);
		echo $source->targetData;
		exit;
	}

	public function actionCompile($url) {

		HTTP::fixGettingQueryString('url');
		$url = strip_tags($_GET['url']);

		try {
			$data = file_get_contents($url);
		} catch (ErrorException $e) {
			throw new BadRequestHttpException('Requested url cannot be received');
		} finally {
			\Yii::error('Request url cannot be resolved: ' . $url, __METHOD__);
		}

		if(empty($data) || $data === '' || $data === ' ' || $data === null)
			throw new BadRequestHttpException('Received data by url '.$url.' is empty');

		$compiler = new GoogleClosure($data);
		$compiler->enableProfiling(true);
		$compiler->execute();

		if($compiler->hasErrors()) {
			$result = array(
				'result' => null,
				'errors' => $compiler->getErrors(),
				'warnings' => $compiler->getWarnings(),
				'compression'=>null,
				'sourceSize'=>null,
				'outputSize'=>null
			);
		} else {
			$result = array(
				'result' => $compiler->getCopyrights() . $compiler->getRuntimeResult(),
				'errors' => null,
				'warnings' => null,
				'compression'=>$compiler->getDifference(true, true),
				'sourceSize'=>$compiler->getSourceSize(),
				'outputSize'=>$compiler->getOutputSize()
			);
		}


		header('Content-type: text/json; charset=UTF-8');
		echo json_encode($result);
	}


	public function actionAdd($username, $user_hash, $data, $params=null) {
		$user = User::findByUsername($username);
		//if(md5($user->username.':'.$user->))
	}

	public function actionUpdate($user_hash, $token, $version, $data) {

	}

	public function actionDelete($token, $version) {

	}
} 