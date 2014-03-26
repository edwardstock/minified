<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * @var \yii\web\View $this
 * @var int|null $groupId
 * @var array $groups
 * @var frontend\modules\storage\models\Source $source
 */
use frontend\modules\storage\models\Source;

$version = $source->isNewRecord ? '1.0' : $source->version;

if(empty($groups)) {
	echo $this->render('select_group',['groups'=>$groups]);
	return;
}


$form = \yii\widgets\ActiveForm::begin([

]);
?>

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-12">
		<h2 class="stylized">Main</h2>

		<div class="dropdown">
			<a data-toggle="dropdown" class="btn btn-default version-button" role="button" href="#">Choose version (Current: <?=$source->version?>)</a>
			<ul class="dropdown-menu version-chooser" role="menu" aria-labelledby="dLabel">
				<?php
					foreach($source->getVersionList($source->publicToken) AS $id=>$version) {
						if($version != intval($version))
							echo '<li><a href="'.\Yii::$app->getUrlManager()->createUrl(['/storage/source/update','id'=>$id]).'"><span class="tree">├</span> '.$version.'</a></li>';
						else
							echo '<li><a href="'.\Yii::$app->getUrlManager()->createUrl(['/storage/source/update','id'=>$id]).'">'.$version.'</a></li>';
					}
				?>
		    </ul>
		</div>
	<?php
	echo $form->errorSummary($source);

	echo $form->field($source, 'groupId')->dropDownList($groups);
	echo $form->field($source, 'title');
	echo $form->field($source, 'type')->dropDownList(Source::getTypes());
	echo $form->field($source, 'sourceData')->textarea([
		'style'=>'max-width: 750px;min-height:236px',
		'class'=>'form-control'
	]);
	echo $form->field($source, 'version', [
		'inputOptions' => [
			'class' => 'form-control',
			'disabled' => 'disabled'
		]
	]);
?>
	</div>

	<div class="col-lg-4 col-md-4 col-sm-12">
		<h2 class="stylized">Settings</h2>
		<br />

		<ul class="list-group">
			<li class="list-group-item">For more information see the documentation for YUICompressor on <a href="http://yui.github.io/yuicompressor/" target="_blank">GitHub</a></li>
			<li class="list-group-item">Obfuscate JS
				<?=$form->field($source,'settings[obfuscateJs]')->dropDownList(Source::booleanValues())?></li>
			<li class="list-group-item">Charset
				<?=$form->field($source,'settings[charset]')->dropDownList(Source::getCharsetList())?></li>
			<li class="list-group-item">Preserve all semicolons
				<?=$form->field($source,'settings[preserveSemicolons]')->dropDownList(Source::booleanValues())?></li>
			<li class="list-group-item">Disable all micro optimizations
				<?=$form->field($source,'settings[disableMicroOptimizations]')->dropDownList(Source::booleanValues())?></li>
		</ul>
	</div>
</div>

<hr />

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<?php
		if(!$source->isNewRecord){
			echo $form->field($source,'updateVersion')->checkbox();
			echo $form->field($source,'updateMajorVersion')->checkbox();
		}

		echo \yii\helpers\Html::submitButton($source->isNewRecord ? 'Create' : 'Save', ['class'=>'btn btn-success'])
		?>
	</div>
</div>
<?php
$form->end();
?>