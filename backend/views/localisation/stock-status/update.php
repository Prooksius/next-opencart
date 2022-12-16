<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = Yii::t('localisation', 'Edit stock status');
$this->params['breadcrumbs'][] = ['label' => Yii::t('localisation', 'Stock statuses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="reviews-update">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
    ]) ?>

</div>
