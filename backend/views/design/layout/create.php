<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Language */

$this->title = YII::t('layout', 'Add layout');
$this->params['breadcrumbs'][] = ['label' => YII::t('layout', 'Layouts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-create">

    <?= $this->render('_form', [
        'model' => $model,
        'allModules' => $allModules,
    ]) ?>

</div>
