<?php

use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = YII::t('order', 'Order #') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => YII::t('order', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$lat = (float)$model->address_latitude;
$lon = (float)$model->address_longitude;

$jscript = <<< JS
  ymaps.ready(init);
  function init() {
    console.log('map init')
      const myMap = new ymaps.Map("map", { center: [{$lat}, {$lon}], zoom: 10 }, { searchControlProvider: 'yandex#search' })
      const myPlacemark = new ymaps.Placemark([{$lat}, {$lon}], {}, { preset: 'islands#icon' })
      myMap.geoObjects.add(myPlacemark)
  }
JS;
$this->registerJs( $jscript, View::POS_READY);
?>
<script src="https://api-maps.yandex.ru/2.1/?apikey=2315e588-5f6f-41c2-a457-f10e0739712a&lang=ru_RU&mode=debug"></script>
<div class="order-view">

  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> <?= YII::t('order', 'Order information') ?></h3>
        </div>
        <table class="table">
          <tbody>
            <tr>
              <td style="width: 1%;">
                <button title="<?= YII::t('order', 'Create Date') ?>" class="btn btn-info btn-xs">
                  <i class="fa fa-calendar fa-fw"></i>
                </button>
              </td>
              <td><?= date('d.m.Y h:i', $model->created_at) ?></td>
            </tr>
            <tr>
              <td style="width: 1%;">
                <button title="<?= YII::t('order', 'Payment method') ?>" class="btn btn-info btn-xs">
                  <i class="fa fa-credit-card fa-fw"></i>
                </button>
              </td>
              <td><?= $model->payment_method ?></td>
            </tr>
            <tr>
              <td style="width: 1%;">
                <button title="<?= YII::t('order', 'Delivery method') ?>" class="btn btn-info btn-xs">
                  <i class="fa fa-truck fa-fw"></i>
                </button>
              </td>
              <td><?= $model->delivery_method ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-user"></i> <?= YII::t('order', 'Customer information') ?></h3>
        </div>
        <table class="table">
          <tbody><tr>
            <td style="width: 1%;"><button title="Покупатель" class="btn btn-info btn-xs"><i class="fa fa-user fa-fw"></i></button></td>
            <?php if ($model->customer_id) { ?>
            <td>
              <?= html::a($model->fullname, ['customer/customer/update', 'id' => $data->id], ['target' => '_blank']) ?>
            </td>
            <?php } else { ?>
            <td><?= $model->fullname ?></td>
            <?php } ?>
          </tr>
          <tr>
            <td><button title="Группа покупателей" class="btn btn-info btn-xs"><i class="fa fa-group fa-fw"></i></button></td>
            <td><?= $model->customerGroup ?></td>
          </tr>
          <tr>
            <td><button title="E-Mail" class="btn btn-info btn-xs"><i class="fa fa-envelope-o fa-fw"></i></button></td>
            <td><a href="mailto:<?= $model->email ?>"><?= $model->email ?></a></td>
          </tr>
          <tr>
            <td><button title="Телефон" class="btn btn-info btn-xs"><i class="fa fa-phone fa-fw"></i></button></td>
            <td><?= $model->phone ?></td>
          </tr>
        </tbody></table>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-info-circle"></i> <?= YII::t('order', 'Order details') ?></h3>
    </div>
    <div class="panel-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <td style="width: 50%;" class="text-left"><?= YII::t('order', 'Order address') ?></td>
            <td style="width: 50%;" class="text-left"><?= YII::t('app', 'Map') ?></td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-left">
              <?= YII::t('order', 'Country')?>: <b><?= $model->address_country ?></b><br />
              <?= YII::t('order', 'Region')?>: <b><?= $model->address_region ?></b><br />
              <?= YII::t('order', 'City')?>: <b><?= $model->address_city ?></b><br />
              <?= YII::t('order', 'Street')?>: <b><?= $model->address_street ?></b><br />
              <?= YII::t('order', 'Postal code')?>: <b><?= $model->address_postcode ?></b><br />
              <?= YII::t('order', 'House')?>: <b><?= $model->address_house ?></b><br />
              <?= YII::t('order', 'Apartnemt')?>: <b><?= $model->address_apartment ?></b><br />
              <?= YII::t('order', 'Geo Coordinates')?>: <b><a href="https://yandex.ru/maps/?pt=<?= $model->address_longitude ?>,<?= $model->address_latitude ?>&z=17&l=map" target="_blank"><?= $model->address_longitude ?>,<?= $model->address_latitude ?></a></b>
            </td>
            <td class="text-left">
              <div id="map" style="height: 350px"></div>
            </td>
          </tr>
        </tbody>
      </table>
      <table class="table table-bordered">
        <thead>
          <tr>
            <td class="text-left"><?= YII::t('product', 'Product') ?></td>
            <td class="text-left"><?= YII::t('product', 'Model') ?></td>
            <td class="text-right"><?= YII::t('product', 'Quantity') ?></td>
            <td class="text-right"><?= YII::t('app', 'Price') ?></td>
            <td class="text-right"><?= YII::t('order', 'Product total') ?></td>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product) { ?>
          <tr>
            <td class="text-left">
              <?= html::a($product['name'], ['catalog/product/update', 'id' => $product['product_id']], ['target' => '_blank']) ?>
              <?php foreach ($product['options'] as $option) { ?>
              <br />
              &nbsp;<small> - <?= $option['name'] ?>: <?= $option['value'] ?></small>
              <?php } ?>
            </td>
            <td class="text-left"><?= $product['model']; ?></td>
            <td class="text-right"><?= $product['quantity']; ?></td>
            <td class="text-right"><?= Yii::$app->currency->format((float)$product['price'], $model->currency_code, (float)$model->currency_value) ?></td>
            <td class="text-right"><?= Yii::$app->currency->format((float)$product['total'], $model->currency_code, (float)$model->currency_value) ?></td>
          </tr>
          <?php } ?>
          <?php foreach ($totals as $total) { ?>
          <tr>
            <td colspan="4" class="text-right"><?= $total['title']; ?></td>
            <td class="text-right"><?= Yii::$app->currency->format((float)$total['value'], $model->currency_code, (float)$model->currency_value) ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php if ($model->comment) { ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <td><?= YII::t('order', 'Comment') ?></td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?= $model->comment; ?></td>
          </tr>
        </tbody>
      </table>
      <?php } ?>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-comment-o"></i> <?= YII::t('order', 'Add Order history') ?></h3>
    </div>
    <div class="panel-body">
      <?= Tabs::widget([
        'items' => [
          [
            'label' => YII::t('order', 'Order history'),
            'content' => $this->render('info/history', [
              'model' => $model,
              'order_statuses' => $order_statuses,
              'order_history' => $order_history,
            ]),
            'active' => true,
          ],
          [
            'label' => YII::t('order', 'Additional info'),
            'content' => $this->render('info/additional', [
              'model' => $model,
            ]),
          ],
        ]
      ]); ?>
    </div>
  </div>
</div>
