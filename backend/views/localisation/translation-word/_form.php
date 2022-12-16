<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\helpers\StringHelper;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use backend\components\InputFileWithPic;

/* @var $this yii\web\View */
/* @var $model app\models\Speciality */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="speciality-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
      <div class="col-sm-6">
        <?= $form->field($model, 'translate_group_id')->dropDownList($model->translateGroupsList)?>
      </div>
      <div class="col-sm-6">
        <?= $form->field($model, 'phrase')->textInput(['maxlength' => true]) ?>
      </div>
    </div>
    <?= Tabs::widget([
        'items' => MyHtml::languageTabs($model, $languages, [
            'name' => 'textarea',
        ]),
        'options' => [
            'id' => 'Speciality-update-tabs',
        ]
    ]);?>
    
    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
