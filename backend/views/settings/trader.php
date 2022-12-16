<?php

use yii\helpers\Html;
use backend\components\MyHtml;

/* @var $this yii\web\View */
/* @var $settings array */

?>
<div class="row">
  <div class="col-sm-6">
    <h4>Торговая система</h4>
    <?= Html::tag(
      'div', 
      Html::label(
          'Биржи') . Html::textarea('trader[stocks]', $trader['stocks'], ['class' => 'form-control', 'rows' => '7']
      ), 
      ['class' => 'form-group'])?>
    <?= Html::tag(
      'div', 
      Html::label(
          'Типы рынка') . Html::textarea('trader[markets]', $trader['markets'], ['class' => 'form-control', 'rows' => '7']
      ), 
      ['class' => 'form-group'])?>
  </div>
  <div class="col-sm-6">
    <h4>Трейдер</h4>
    <?= MyHtml::formGroup('trader', 'referals_start', 'Окно подключения, дн.', $trader['referals_start'])?>
    <?= MyHtml::formGroup('trader', 'referals_period', 'Срок подключения, мес.', $trader['referals_period'])?>
    <?= MyHtml::formGroup('trader', 'platform_fee', 'Platform Fee, %', $trader['platform_fee'])?>
  </div>
</div>