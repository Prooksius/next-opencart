<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = 'Добавить отзыв';
$this->params['breadcrumbs'][] = ['label' => 'Отзывы о нас', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
