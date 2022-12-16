<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = Yii::t('product', 'Edit product attribute');
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Product attributes'), 'url' => ['index', 'product_id' => $model->product_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="reviews-update">

    <?= $this->render('_form', [
        'model' => $model,
        'allFilters' => $allFilters,
    ]) ?>

</div>
