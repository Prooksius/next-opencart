<?php

use yii\helpers\Html;
use backend\components\MyHtml;

/* @var $this yii\web\View */
/* @var $settings array */

?>
<?= MyHtml::formGroup('social', 'wa', 'Ссылка Watsapp', $social['wa'])?>
<?= MyHtml::formGroup('social', 'tg', 'Ссылка Telegram', $social['tg'])?>
