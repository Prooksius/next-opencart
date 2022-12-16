<?php

use yii\helpers\Html;
use backend\components\MyHtml;

/* @var $this yii\web\View */
/* @var $settings array */

?>
<?= MyHtml::formGroup('trips', 'max_members', 'Максимальное количество участников', $trips['max_members'])?>
