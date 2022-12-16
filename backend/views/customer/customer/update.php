<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'Просмотреть/изменить пользователя' . ': ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['customer']];
$this->params['breadcrumbs'][] = 'Изменить';

?>
<div class="customer-update">
  <?= Tabs::widget([
    'items' => [
      [
        'label' => 'Общие данные',
        'content' => $this->render('_form', [
          'model' => $model,
          'allGroups' => $allGroups,
          'mode' => 'update',
          'settings' => $settings,
        ]),
        'active' => true,
      ],
    ],
  ]);?>
</div>