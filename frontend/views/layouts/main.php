<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
	<?=$this->registerCssFile('/css/style.css')?>
</head>
<body>
<?php
$profiling = microtime(true);
\Yii::$app->minified->prepare()->rock($this);
$profiling = microtime(true) - $profiling;
\Yii::info("Profiling: ".number_format($profiling,3).' seconds', 'MINIFIED EXTENSION');
?>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            echo $this->render('@app/views/layouts/menu');

            if(\Yii::$app->user->isGuest === false)
                echo $this->render('@app/views/layouts/user_menu');
        ?>

        <div class="container main-workspace">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
	        'id'=>'breadcrumbs',
	       // 'separator'=>'<span class="b-sep"> :: </span>'
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; <strong>MINIFIED.pw</strong> | <a target="_blank" href="http://redstardesign.ru">RED STAR DESIGN</a> <?= date('Y') ?></p>
        <p class="pull-right">Powered by Yii Framework, Yahoo, Oracle</p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
