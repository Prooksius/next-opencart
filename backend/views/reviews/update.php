<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = 'Изменить отзыв: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Отзывы о нас', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="reviews-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
