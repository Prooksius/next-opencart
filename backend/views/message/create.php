<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Payout */

$this->title = 'Создать сообщение';
$this->params['breadcrumbs'][] = ['label' => 'Пользовательские сообщения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payout-create">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
    ]) ?>

</div>
