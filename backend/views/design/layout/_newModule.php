<?php

use yii\helpers\Html;
use \backend\components\InputFileWithPic;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

?>
<tr id="modules-record-<?= $position ?>-<?= $key ?>">
  <td style="width: 1px">
    <i style="padding: 5px; cursor: grab; font-size: 18px;" class="fa fa-bars ui-sortable-handle"></i>
  </td>
  <td style="width: 100%;">
    <?= Html::dropDownList(
      StringHelper::basename(get_class($model)) . '[modules]['.$position.']['.$key.'][module_id]', 
      $module['module_id'],
      $allModules,
      ['class' => 'form-control'])
    ?>
    <?= Html::hiddenInput(
      StringHelper::basename(get_class($model)) . '[modules]['.$position.']['.$key.'][sort_order]',
      $key
    )?>
  </td>
  <td style="width: 1px"><button type="button" class="btn btn-danger" onclick="$(this).closest('tr').remove();" title="<?= YII::t('layout', 'Delete module') ?>">&times;</button></td>
</tr>
