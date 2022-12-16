<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = 'Добавить атрибут';
$this->params['breadcrumbs'][] = ['label' => 'Атрибуты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-create">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'allGroups' => $allGroups,
    ]) ?>

</div>
