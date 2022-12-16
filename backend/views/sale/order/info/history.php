<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Speciality */

$id = (int)$model->id;

$jscript = <<< JS
  $('#history').load('/admin/sale/order/history?id=' + {$id})

  $('#button-history').on('click', function() {

    $.ajax({
      url: '/admin/sale/order/add-history?id=' + {$id},
      type: 'post',
      dataType: 'json',
      data: 'order_status_id=' + encodeURIComponent($('select[name="order_status_id"]').val()) + '&notify=' + ($('input[name="notify"]').prop('checked') ? 1 : 0) + '&comment=' + encodeURIComponent($('textarea[name="comment"]').val()),
      success: function(json) {
        $('.alert').remove();

        if (json['error']) {
          $('#history').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        }

        if (json['success']) {
          $('#history').load('/admin/sale/order/history?id=' + {$id})

          $('#history').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

          $('textarea[name=\'comment\']').val('');
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\
" + xhr.statusText + "\
" + xhr.responseText);
      }
    });
  });  
JS;
$this->registerJs( $jscript, View::POS_READY);

?>
<div id="history"></div>
<br />
<fieldset>
  <legend><?= YII::t('order', 'Manage Order history') ?></legend>
  <form class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-2 control-label" for="input-order-status"><?= YII::t('order', 'Order Status') ?></label>
      <div class="col-sm-10">
        <?= Html::dropDownList('order_status_id', (int)$model->order_status_id, $order_statuses, ['class' => 'form-control']) ?>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="input-notify"><?= YII::t('order', 'Notify') ?></label>
      <div class="col-sm-10">
        <?= Html::checkbox('notify', false) ?>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="input-comment"><?= YII::t('order', 'Comment') ?></label>
      <div class="col-sm-10">
        <?= Html::textarea('comment', '', ['class' => 'form-control', 'rows' => 8]) ?>
      </div>
    </div>
  </form>
</fieldset>
<div class="text-right">
  <button id="button-history" data-loading-text="<?= YII::t('app', 'Loading...') ?>"class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?= YII::t('order', 'Add Order history') ?></button>
</div>