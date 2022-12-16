<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = Yii::t('localisation', 'Edit order status');
$this->params['breadcrumbs'][] = ['label' => Yii::t('localisation', 'Order statuses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="reviews-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
