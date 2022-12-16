<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = Yii::t('product', 'Add special price');
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Special prices'), 'url' => ['index', 'product_id' => $product_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-create">

    <?= $this->render('_form', [
        'model' => $model,
        'product_id' => $product,
        'allGroups' => $allGroups,
    ]) ?>

</div>
