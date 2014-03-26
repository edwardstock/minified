<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: SourceController
 */

namespace frontend\modules\storage\controllers;

use common\helpers\ES;
use frontend\components\UserDataController;
use frontend\modules\storage\models\Source;
use frontend\modules\storage\models\SourceGroup;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\HttpException;

class SourceController extends UserDataController
{

	public function actionCreate() {

		if ( isset($_POST['SourceGroup']) && !empty($_POST['SourceGroup']) ) {
			$this->redirect(\Yii::$app->getUrlManager()->createUrl(['/source/group/create', \Yii::$app->getRequest()->post()]));
		}

		$source = new Source();

		if(\Yii::$app->request->isPost && $source->load($_POST) && $source->validate()) {
			$source->save();
			$source->appendToken();
			$this->redirect(\Yii::$app->getUrlManager()->createUrl(['/storage/source/list']));
		}

		return $this->render('_item',[
			'groupId'=>null,
			'groups'=>ArrayHelper::map(SourceGroup::find()->all(),'id','title'),
			'source'=>$source
		]);
	}

	public function actionList() {

		$sources = Source::getList();

		return $this->render('index', [
			'dataProvider' => $sources
		]);
	}

	public function actionUpdate() {
		if(!isset($_GET['id']))
			throw new HttpException(400,'Bad request');

		$id = (int) $_GET['id'];
		/** @var Source $source */
		$source = Source::find($id);

		if($source === null)
			throw new HttpException(404,'Source not found');

		if(\Yii::$app->request->isPost && $source->load($_POST) && $source->validate()) {
			$source->save();
			$this->redirect(\Yii::$app->getUrlManager()->createUrl(['/storage/source/list']));
		}

		return $this->render('_item',[
			'groupId'=>null,
			'groups'=>ArrayHelper::map(SourceGroup::find()->all(),'id','title'),
			'source'=>$source
		]);

	}

	public function actionDelete() {

	}

	public function actionView() {

	}
} 