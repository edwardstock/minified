<?php

/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * @var \yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
$this->title = 'My groups';
$this->params['breadcrumbs'] = [
	[
		'label'=>'Profile',
		'url'=>['/user/profile']
	],
	'Groups'
];

?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<a href="/storage/group/create" class="btn btn-primary">Add group</a>
	</div>
</div>

<?php
echo \yii\grid\GridView::widget([
	'dataProvider'=>$dataProvider,
	'columns'=>[
		[
			'label'=>'Title',
			'format'=>'html',
			'value'=>function(\frontend\modules\storage\models\SourceGroup $group){
				return \yii\helpers\Html::a($group->title,['/source/group/update','id'=>$group->id]);
			}
		],
		[
			'label'=>'Source count',
			'value'=>function(\frontend\modules\storage\models\SourceGroup $group){
				return count($group->storages);
			}
		],

		[
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{update} {delete}',
		],
	],
]);