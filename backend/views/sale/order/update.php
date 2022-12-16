<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Speciality */


$this->title = YII::t('order', 'Update Order');
$this->params['breadcrumbs'][] = ['label' => YII::t('order', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = YII::t('app', 'Update');
?>
<div class="speciality-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
