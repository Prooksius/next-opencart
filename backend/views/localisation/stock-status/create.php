<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = Yii::t('localisation', 'Add stock status');
$this->params['breadcrumbs'][] = ['label' => Yii::t('localisation', 'Stock statuses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-create">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
    ]) ?>

</div>
