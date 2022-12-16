<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $merchant array */

?>
<div class="row">
  <div class="col-md-4">
    <?= MyHtml::formGroup('merchant', 'merchant_id', 'Merchant ID', $merchant['merchant_id'])?>
  </div>
  <div class="col-md-4">
    <?= MyHtml::formGroup('merchant', 'api_id', 'API ID', $merchant['api_id'])?>
  </div>
  <div class="col-md-4">
    <?= MyHtml::formGroup('merchant', 'currency', 'Currency', $merchant['currency'])?>
  </div>
  <div class="col-md-4">
    <?= MyHtml::formGroup('merchant', 'merchant_secret', 'Merchant Secret', $merchant['merchant_secret'])?>
  </div>
  <div class="col-md-4">
    <?= MyHtml::formGroup('merchant', 'api_secret', 'API Secret', $merchant['api_secret'])?>
  </div>
  <div class="col-md-4">
    <?= MyHtml::formGroupSelect('merchant', 'mode', 'Режим работы', $merchant['mode'], [0 => 'Боевой', 1 => 'Тестовый'])?>
  </div>
</div>
