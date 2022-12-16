<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Payout */

$this->title = 'Изменить сообщение: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Пользовательские сообщения', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payout-update">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
    ]) ?>

</div>
