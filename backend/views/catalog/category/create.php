<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = YII::t('category', 'Add Category');
$this->params['breadcrumbs'][] = ['label' => YII::t('category', 'Categoris'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-create">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'catsTree' => $catsTree
    ]) ?>

</div>
