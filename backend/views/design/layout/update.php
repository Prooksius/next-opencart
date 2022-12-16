<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Language */

$this->title = YII::t('layout', 'Update layout') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => YII::t('layout', 'Layouts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = YII::t('app', 'Update');
?>
<div class="language-update">

    <?= $this->render('_form', [
        'model' => $model,
        'allModules' => $allModules,
    ]) ?>

</div>
