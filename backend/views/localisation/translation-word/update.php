<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Speciality */


$this->title = YII::t('app', 'Update translation');
$this->params['breadcrumbs'][] = ['label' => YII::t('app', 'Translations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = YII::t('app', 'Update');
?>
<div class="speciality-update">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
    ]) ?>

</div>
