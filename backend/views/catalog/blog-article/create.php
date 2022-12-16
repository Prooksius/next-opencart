<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = YII::t('blog', 'Add Blog Article');
$this->params['breadcrumbs'][] = ['label' => YII::t('blog', 'Blog Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
