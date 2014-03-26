<?php
use yii\widgets\ActiveForm;

/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 * @var \yii\web\View $this
 * @var \frontend\modules\storage\models\SourceGroup[] $groups
 *
 */

if(empty($groups)) {
	echo '<h1>You are not have configured groups</h1>';
	echo '<h3><button onclick="$(\'#w0\').modal()" class="btn btn-default">Do you want create one?</button></h3>';
}

$modal = \yii\bootstrap\Modal::begin([
	'header'=>'Creating group',
	'size'=>\yii\bootstrap\Modal::SIZE_SMALL
]);

	echo $this->render('@app/modules/source/views/group/_item',[
		'group'=>new \frontend\modules\storage\models\SourceGroup()
	], $modal);

\yii\bootstrap\Modal::end();