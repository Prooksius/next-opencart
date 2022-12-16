<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Pages */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $form->field($model, 'main_category_id')->dropDownList($model->categoriesTree) ?>

<div class="form-group well-group">
  <label class="control-label"><?= Yii::t('product', 'Show Categories') ?></label>
  <div class="well well-sm" style="min-height: 150px;max-height: 500px;overflow: auto;">
    <table class="table table-striped">
    <?php foreach ($model->categoriesTree as $category_id => $category_name) { ?>
      <?php if ((int)$category_id) { ?>
      <tr>
        <td class="checkbox">
          <label>
            <?= Html::checkbox(StringHelper::basename(get_class($model)) . '[categoryIds][]', in_array($category_id, $model->categoryIds), ['value' => $category_id]) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $category_name ?>
          </label>
        </td>
      </tr>
      <?php } ?>
    <?php } ?>
    </table>
  </div>
  <a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?= Yii::t('app', 'Select All') ?></a> / 
  <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?= Yii::t('app', 'Unselect All') ?></a>
</div>
