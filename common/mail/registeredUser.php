<?php
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var frontend\modules\user\models\User $user
 */

?>

<?=ucfirst($user->username)?>, welcome to MINIFIED.pw!

For continue registration, you must verify your email address by following link:
<?=$user->getActivationLink()?>