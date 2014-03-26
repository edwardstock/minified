<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var string $name
 * @var string $message
 * @var Exception $exception
 */

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <h2><?= nl2br(Html::encode($message)) ?></h2>
	    <p>Please check your request parameters or notify us about this error by email: <strong>bug{at}minified.pw</strong></p>
    </div>

</div>
