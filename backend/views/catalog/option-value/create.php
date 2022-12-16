<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */

$this->title = Yii::t('option', 'Add option value');
$this->params['breadcrumbs'][] = ['label' => Yii::t('option', 'Option Values'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-create">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'option_id' => $option_id,
    ]) ?>

</div>
