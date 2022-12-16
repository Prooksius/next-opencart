<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sef */

$this->title = 'Создать ссылку';
$this->params['breadcrumbs'][] = ['label' => 'СЕО-ссылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sef-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
