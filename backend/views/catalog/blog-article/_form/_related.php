<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\helpers\StringHelper;
use backend\components\MyProductSelect;

/* @var $this yii\web\View */
/* @var $model app\models\Pages */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $form->field($model, 'relatedIds')->widget(MyProductSelect::className(), [
    'id' => 'product-select',
]) ?>
