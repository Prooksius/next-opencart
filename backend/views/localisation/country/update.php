<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = YII::t('app', 'Update country');
$this->params['breadcrumbs'][] = ['label' => YII::t('app', 'Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = YII::t('app', 'Update');
?>
<div class="reviews-update">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
    ]) ?>

</div>
