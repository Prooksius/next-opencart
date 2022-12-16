<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = Yii::t('product', 'Add product attribute');
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Product attributes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-create">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'product_id' => $product,
        'attributesList' => $attributesList,
    ]) ?>

</div>
