<?php

use yii\helpers\Html;
use \backend\components\InputFileWithPic;
use yii\helpers\StringHelper;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

?>
<table class="table table-striped table-hover" id="layout-<?= $position ?>">
  <thead>
    <tr>
      <th style="width: 1px"></th>
      <th><?= $title ?></th>
      <th style="width: 1px"></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($model->modules[$position] as $key => $module) { ?>
      <?= $this->render('_newModule', [
        'model' => $model,
        'module' => $module,
        'allModules' => $allModules,
        'position' => $position,
        'key' => $key
      ]) ?>
    <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <td></td>
        <td class="add-module-btn" data-position="<?= $position ?>"><?= YII::t('layout', 'Add module') ?></td>
        <td><button type="button" class="btn btn-success add-module-btn" data-position="<?= $position ?>" title="<?= YII::t('layout', 'Add module') ?>">+</button></td>
      </tr>
    </tfoot>
</table>
