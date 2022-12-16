<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = YII::t('blog', 'Edit Blog Article');
$this->params['breadcrumbs'][] = ['label' => YII::t('blog', 'Blog Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = YII::t('app', 'Update');
?>
<div class="reviews-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
