<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Language */

$this->title = YII::t('mainslider', 'Add Slider');
$this->params['breadcrumbs'][] = ['label' => YII::t('mainslider', 'Sliders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-create">

    <?= $this->render('_form', [
        'model' => $model,
        'banners' => $banners,
    ]) ?>

</div>
