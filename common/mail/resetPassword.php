<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 24.04.2020
 * Time: 12:32
 */

use yii\helpers\Html;

echo 'Привет, '. Html::encode($user->username) . '. ';
echo Html::a('Для смены пароля перейдите по этой ссылке.',
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            'site/reset-password',
            'key' => $user->secret_key
        ]
    )
);