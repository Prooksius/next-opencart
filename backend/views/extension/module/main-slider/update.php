<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Language */

$this->title = YII::t('module', 'Edit module') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => YII::t('mainslider', 'Sliders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = YII::t('app', 'Update');
?>
<div class="language-update">

    <?= $this->render('_form', [
        'model' => $model,
        'banners' => $banners,
    ]) ?>

</div>
