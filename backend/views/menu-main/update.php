<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MenuPrices */


$this->title = 'Изменить пункт меню';
$this->params['breadcrumbs'][] = ['label' => 'Меню страницы Цены', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="menu-prices-update">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
    ]) ?>

</div>
