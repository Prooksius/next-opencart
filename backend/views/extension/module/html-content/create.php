<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Language */

$this->title = YII::t('module', 'Add module');
$this->params['breadcrumbs'][] = ['label' => YII::t('html', 'HTML Content'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
