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
	'Storages'
];

use \frontend\modules\storage\models\Source;

?>
	<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<?=\yii\helpers\Html::a('Add source',['/storage/source/create'],['class'=>'btn btn-default'])?>
	</div>
	<hr />
</div>

<?php
echo \yii\grid\GridView::widget([
	'dataProvider'=>$dataProvider,
	'columns'=>[
		[
			'label'=>'Title',
			'format'=>'html',
			'value'=>function(Source $source){
				return $source->title;
			}
		],

		[
			'label'=>'url',
			'format'=>'raw',
			'value'=>function(Source $source) {
				return '<input class="select-url" value="'.$source->getRawUrl().'" onclick="this.select();">';
			}
		],

		[
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{view} {update} {delete}',
		],
	],
]);