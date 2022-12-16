<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\InputFileWithPic;
use dosamigos\datetimepicker\DateTimePicker;
use backend\components\MyCustomerChoose;
use common\models\Customer;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="customer-form">

  <?php $form = ActiveForm::begin(); ?>

  <div class="row">
    <div class="col-md-4 col-sm-12">
      <div class="picture-div">
        <label class="control-label">Аватар</label>
        <?= InputFileWithPic::widget([
          'language'     => 'ru',
          'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
          'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
          'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
          'template'      => '<div class="picture">{picture}</div>{input}<div class="btn-group">{browse_button}{clear_button}</div>',
          'options'       => ['class' => 'form-control', 'id' => 'product2-picture'],
          'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
          'clearButtonName'    => '<span class="glyphicon glyphicon-erase"></span>',
          'buttonOptions' => ['class' => 'btn btn-primary', 'title' => 'Выбрать изображение'],
          'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => 'Очистить'],
          'multiple'      => false,       // возможность выбора нескольких файлов
          'name'       => 'Customer[picture]',
          'value'      => $model->picture,
        ]); ?>
      </div>      
    </div>
    <div class="col-md-4 col-sm-6">
      <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->label(($mode == 'create' ? 'Пароль' : 'Новый пароль')) ?>
      <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4 col-sm-6">
      <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'telegram')->textInput(['maxlength' => true]) ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4 col-sm-6">
      <?= $form->field($model, 'customer_group_id')->dropDownList($allGroups) ?>
      <?= $form->field($model, 'ref_link')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4 col-sm-6">
      <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'email_confirmed')->dropDownList([0 => 'Нет', 1 => 'Да']) ?>
    </div>
    <div class="col-md-4 col-sm-6">
      <?= $form->field($model, 'lastvisit')->widget(DateTimePicker::classname(), [
                'language' => 'ru',
                'size' => 'ms',
                'template' => '{input}',
                'pickButtonIcon' => 'glyphicon glyphicon-time',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy hh:ii',
                    'todayBtn' => true
                ],
            ]); ?>
    </div>
  </div>

  <div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Отмена', Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>