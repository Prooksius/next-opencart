<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = Yii::t('filter', 'Edit Filter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('filter', 'Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="reviews-update">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'allGroups' => $allGroups,
    ]) ?>

</div>
