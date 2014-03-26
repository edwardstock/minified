<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 * @var frontend\modules\storage\models\SourceGroup $model
 * @var \yii\web\View $this
 */
\frontend\assets\ProfileAsset::register($this);
?>
<div class="col-lg-12 col-md-12 col-sm-12 group-item">
	<h1><?= $model->title ?></h1>
	<div class="group-storages">
	<?php
		if ( empty($model->storages) ) {
			echo 'Отсутствуют данные';
		}

		foreach ( $model->storages AS $storage ) {

		}
	?>
	</div>
</div>