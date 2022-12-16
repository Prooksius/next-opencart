<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = Yii::t('filter', 'Add Filter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('filter', 'Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-create">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'filter_group_id' => $filter_group_id,
    ]) ?>

</div>
