<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: GroupController
 */

namespace frontend\modules\storage\controllers;


use frontend\components\UserDataController;
use frontend\modules\storage\models\SourceGroup;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;

class GroupController extends UserDataController
{

	public function actionCreate() {

		if ( isset($_GET[0]['SourceGroup']) && !empty($_GET[0]['SourceGroup']) ) {
			$group = new SourceGroup();
			$group->setAttributes($_GET[0]['SourceGroup']);
			if ( $group->validate() && $group->save() ) {
				$this->redirect(\Yii::$app->urlManager->createUrl(['/source/item/create']));
			} else {
				throw new HttpException(500, 'Error while creating group');
			}
		} else {
			return $this->actionUpdate(true);
		}
	}

	public function actionUpdate($new = false) {
		if ( $new ) {
			$group = new SourceGroup();
		} else {
			if ( !isset($_GET['id']) ) {
				throw new HttpException(404, 'Group not found');
			}

			$id = (int)$_GET['id'];
			$group = SourceGroup::find($id);

			if ( $group === null ) {
				throw new HttpException(404, 'Group not found');
			}
		}

		if ( $group->load($_POST) && $group->validate() ) {
			$group->save();
			$this->redirect(
				\Yii::$app->getUrlManager()->createUrl(['/source/group'])
			);
		}


		return $this->render('_item', [
			'group' => $group
		]);
	}

	public function actionIndex() {
		$groups = new ActiveDataProvider([
			'query' => SourceGroup::find()->with('storages')
		]);

		return $this->render('index', [
			'dataProvider' => $groups
		]);
	}

	public function actionDelete() {

	}
} 