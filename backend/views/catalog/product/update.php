<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = YII::t('product', 'Edit Product');
$this->params['breadcrumbs'][] = ['label' => YII::t('product', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = YII::t('app', 'Update');
?>
<div class="reviews-update">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'catsTree' => $catsTree,
        'manufacturers' => $manufacturers
    ]) ?>

</div>
