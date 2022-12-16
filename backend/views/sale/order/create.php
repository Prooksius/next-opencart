<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Speciality */

$this->title = YII::t('order', 'Add new Order');
$this->params['breadcrumbs'][] = ['label' => YII::t('order', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="speciality-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
