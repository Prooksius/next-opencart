<?php

use yii\helpers\Html;
use \backend\components\InputFileWithPic;
use yii\helpers\StringHelper;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

$recs_no = count($model->images[$language->locale]);
$lang = $language->locale;
$lang_code = $language->code;

$jscript = <<< JS
  var imageRecord{$lang_code} = {$recs_no};
	let csrfP{$lang_code} = $('meta[name="csrf-param"]').attr('content')
	let csrfT{$lang_code} = $('meta[name="csrf-token"]').attr('content')

  $('#add-record-{$lang_code}').on('click', function() {
    $.ajax({
      url: '/admin/design/banner/new-record',
      dataType: 'html',
      type: 'post',
      data: {
        csrfP{$lang_code}: csrfT{$lang_code},
        language: '{$lang}',
        recordNum: imageRecord{$lang_code},
      },
      success: function (result) {
        if (result) {
          $('#banner-{$lang} tbody').append(result)
          imageRecord{$lang_code}++
        }
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(thrownError + xhr.statusText + xhr.responseText)
      },
    })
  })
JS;

$this->registerJs( $jscript, View::POS_READY);

?>
<div class="table-responsive">
  <table class="table table-striped" id="banner-<?= $language->locale ?>">
    <thead>
      <tr>
        <th style="width: 40%"><?= YII::t('app', 'Title & texts') ?></th>
        <th><?= YII::t('app', 'link')?></th>
        <th style="width: 167px"><?= YII::t('app', 'Image')?></th>
        <th style="width: 160px"><?= YII::t('app', 'Sort Order')?></th>
        <th style="width: 1px"></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($model->images[$language->locale] as $key => $image) { ?>
        <?= $this->render('_newRecord', [
          'model' => $model,
          'image' => $image,
          'language' => $language->locale,
          'key' => $key
        ]) ?>
      <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="4"></td>
          <td><button type="button" class="btn btn-success" id="add-record-<?= $lang_code ?>" title="<?= YII::t('banner', 'Add banner') ?>">+</button></td>
        </tr>
      </tfoot>
  </table>
</div>
