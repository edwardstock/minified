<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 * @var \yii\web\View $this
 * @var \frontend\modules\storage\models\SourceGroup $group
 * @var string $redirect
 */
$this->title = 'Editing group';
$this->params['breadcrumbs'] = [
	[
		'label'=>'Profile',
		'url'=>['/user/profile']
	],
	[
		'label'=>'Groups',
		'url'=>['/source/group'],
	],
	'Group editing'
];
$form = \yii\widgets\ActiveForm::begin([

]);
	if($group->isNewRecord) {
		echo $form->field($group,'title', ['inputOptions'=>['value'=>'Common','class'=>'form-control']]);
	} else {
		echo $form->field($group,'title');
	}

	echo $form->field($group, 'description')->textarea();
	echo \yii\helpers\Html::submitButton(\Yii::t('buttons',$group->isNewRecord ? 'Create':'Save'), ['class'=>'btn btn-primary']);

$form->end();