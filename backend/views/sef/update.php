<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sef */

$this->title = 'Изменить ссылку: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'СЕО-ссылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="sef-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
