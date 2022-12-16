<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 24.06.2019
 * Time: 9:38
 */

use yii\helpers\Html;
use yii\helpers\Url;

?>
    <h2>Здравствуйте.</h2>
    <p>Посетитель заполнил форму</p>
    <p>Заполненные данные:</p>
    <p>Имя: <b><?= $name; ?></b><br />
    Email: <b><?= $email; ?></b><br />
    Телефон: <b><?= $phone; ?></b></p>
    <p>Форма отправлена со страницы <?= Html::a(Yii::$app->request->hostInfo . $page, Yii::$app->request->hostInfo . $page) ?></p>