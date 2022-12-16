<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = $base_url . 'lk/confirm-email' . '?token=' . $user->password_reset_token;
?>
Здравствуйте <?= $user->username ?>,

Перейдите по следующей ссылке для подтверждения почты:

<?= $resetLink ?>