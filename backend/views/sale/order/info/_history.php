<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Speciality */

?>
<div class="table-responsive">
  <table class="table table-bordered">
    <thead>
      <tr>
        <td class="text-left"><?= YII::t('order', 'Create Date') ?></td>
        <td class="text-left"><?= YII::t('order', 'Comment') ?></td>
        <td class="text-left"><?= YII::t('order', 'Order Status') ?></td>
        <td class="text-left"><?= YII::t('order', 'Customer informed') ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($order_history as $item) { ?>
      <tr>
        <td class="text-left"><?= date('d.m.Y H:i', (int)$item['created_at']) ?></td>
        <td class="text-left"><?= $item['comment'] ?></td>
        <td class="text-left"><?= $order_statuses[(int)$item['order_status_id']] ?></td>
        <td class="text-left"><?= YII::t('app', ((int)$item['notify'] ? 'Yes' : 'No')) ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
