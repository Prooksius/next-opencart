<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Language */

$this->title = YII::t('banner', 'Add banner');
$this->params['breadcrumbs'][] = ['label' => YII::t('banner', 'Banners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
