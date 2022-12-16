<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MenuPrices */

$this->title = 'Добавить новый пункт меню';
$this->params['breadcrumbs'][] = ['label' => 'Меню страницы Цены', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-prices-create">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
    ]) ?>

</div>
