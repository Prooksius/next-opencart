<?php

use yii\helpers\Html;
use \backend\components\InputFileWithPic;
use yii\helpers\StringHelper;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

$title = [
  'page-top' => YII::t('layout', 'Page Top'),
  'column-left' => YII::t('layout', 'Column Left'),
  'column-right' => YII::t('layout', 'Column Right'),
  'content-top' => YII::t('layout', 'Content Top'),
  'content-bottom' => YII::t('layout', 'Content Bottom'),
  'page-bottom' => YII::t('layout', 'Page Bottom'),
];

$recs_no_pagetop = !empty($model->modules['page-top']) ? count($model->modules['page-top']) : 0;
$recs_no_left = !empty($model->modules['column-left']) ? count($model->modules['column-left']) : 0;
$recs_no_right = !empty($model->modules['column-right']) ? count($model->modules['column-right']) : 0;
$recs_no_top = !empty($model->modules['content-top']) ? count($model->modules['content-top']) : 0;
$recs_no_bottom = !empty($model->modules['content-bottom']) ? count($model->modules['content-bottom']) : 0;
$recs_no_pagebottom = !empty($model->modules['page-bottom']) ? count($model->modules['page-bottom']) : 0;

$jscript = <<< JS
	let csrfPLayout = $('meta[name="csrf-param"]').attr('content')
	let csrfTLayout = $('meta[name="csrf-token"]').attr('content')

  var imageRecords = {
    'page-top': {$recs_no_pagetop},
    'column-left': {$recs_no_left},
    'column-right': {$recs_no_right},
    'content-top': {$recs_no_top},
    'content-bottom': {$recs_no_bottom},
    'page-bottom': {$recs_no_pagebottom},
  };

  $('.add-module-btn').on('click', function() {

    const position = $(this).attr('data-position')

    $.ajax({
      url: '/admin/design/layout/new-record',
      dataType: 'html',
      type: 'post',
      data: {
        csrfPLayout: csrfTLayout,
        position: position,
        recordNum: imageRecords[position],
      },
      success: function (result) {
        if (result) {
          $('#layout-'+position+' tbody').append(result)
          imageRecords[position]++
        }
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(thrownError + xhr.statusText + xhr.responseText)
      },
    })
  })

  $('#layout-page-top tbody, #layout-content-top tbody, #layout-column-left tbody, #layout-column-right tbody, #layout-content-bottom tbody, #layout-page-bottom tbody').sortable({
    placeholder: "ui-state-highlight",
    revert: 150,
    handle: "i.fa-bars",
    tolerance: "pointer",
    axis: "y",
    start: function( event, ui ) {
      ui.placeholder.height(ui.item.height());
    },
    stop: function( event, ui ) {
      let item_no = 0;
      ui.item.closest('tbody').find('tr').each( function() {
        let sort_order_input = $(this).find('[name*="sort_order"]');
        if (sort_order_input.length) {
          sort_order_input.val(item_no);
        }
        item_no++;
      });
    }
  });

JS;

$this->registerJs( $jscript, View::POS_READY);

?>
<div class="row">
  <div class="col-md-12">
    <div class="position-content margin-bottom">
      <?= $this->render('_modulesPos', [
        'title' => $title['page-top'],
        'model' => $model,
        'allModules' => $allModules,
        'position' => 'page-top',
      ]) ?>
    </div>
  </div>
  <div class="col-md-4">
    <div class="position-content margin-bottom">
      <?= $this->render('_modulesPos', [
        'title' => $title['column-left'],
        'model' => $model,
        'allModules' => $allModules,
        'position' => 'column-left',
      ]) ?>
    </div>
  </div>
  <div class="col-md-4">
    <div class="position-content">
      <?= $this->render('_modulesPos', [
        'title' => $title['content-top'],
        'model' => $model,
        'allModules' => $allModules,
        'position' => 'content-top',
      ]) ?>
    </div>
    <div class="layout-page-content"><?= Yii::t('layout', 'Page Content')?></div>
    <div class="position-content margin-bottom">
      <?= $this->render('_modulesPos', [
        'title' => $title['content-bottom'],
        'model' => $model,
        'allModules' => $allModules,
        'position' => 'content-bottom',
      ]) ?>
    </div>
  </div>
  <div class="col-md-4">
    <div class="position-content margin-bottom">
      <?= $this->render('_modulesPos', [
        'title' => $title['column-right'],
        'model' => $model,
        'allModules' => $allModules,
        'position' => 'column-right',
      ]) ?>
    </div>
  </div>
  <div class="col-md-12">
    <div class="position-content margin-bottom">
      <?= $this->render('_modulesPos', [
        'title' => $title['page-bottom'],
        'model' => $model,
        'allModules' => $allModules,
        'position' => 'page-bottom',
      ]) ?>
    </div>
  </div>
</div>