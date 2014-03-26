<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * @var yii\web\View $this
 * @var frontend\modules\storage\models\SourceGroup[] $groups
 *
 */

$this->title = "Dashboard";
$this->params['breadcrumbs'][] = 'Dashboard';

if(empty($groups)) {
	echo '<h1>You are not have any scripts or styles</h1>';
	echo '<h3>'.\yii\helpers\Html::a('Lets create?',['/source/item/create']).'</h3>';
}


echo \yii\widgets\ListView::widget([
	'dataProvider'=>$groups,
	'itemView'=>'_group_item',
]);


