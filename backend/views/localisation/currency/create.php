<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Language */

$this->title = YII::t('currency', 'Add currency');
$this->params['breadcrumbs'][] = ['label' => YII::t('currency', 'Currencies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
