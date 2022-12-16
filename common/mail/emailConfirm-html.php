<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = $base_url . 'lk/confirm-email'. '?token=' . $user->password_reset_token;
?>
<div class="password-reset">
    <p>Здравствуйте, <?= Html::encode($user->username) ?>,</p>

    <p>Перейдите по следующей ссылке для подтверждения почты:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
