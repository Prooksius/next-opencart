<?php

use yii\helpers\Html;
use \backend\components\InputFileWithPic;
use dosamigos\datetimepicker\DateTimePicker;
use backend\components\MyHtml;
use yii\helpers\StringHelper;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Pages */
/* @var $form yii\widgets\ActiveForm */

$url = \Yii::$app->urlManager->baseUrl;
$format = <<< SCRIPT
function format(state) {
    if (!state.id) return state.text; // optgroup
    console.log('state', state)
    const titles = state.text.split('###')
    src = '$url' +  state.id.toLowerCase() + '.png'
    return '<img style="max-width: 20px; margin-right: 10px;" src="' + titles[1] + '"/>' + titles[0];
}
SCRIPT;
$escape = new JsExpression("function(m) { return m; }");
$this->registerJs($format, View::POS_HEAD);

?>
<div class="row">
    <div class="col-md-4 col-sm-6">
        <div class="picture-div">
            <label class="control-label"><?= $model->getAttributeLabel('image') ?></label>
            <?= InputFileWithPic::widget([
                'language'     => 'ru',
                'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
                'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                'template'      => '<div class="picture">{picture}</div>{input}<div class="btn-group">{browse_button}{clear_button}</div>',
                'options'       => ['class' => 'form-control', 'id' => 'pages-image'],
                'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
                'clearButtonName'    => '<span class="glyphicon glyphicon-erase"></span>',
                'buttonOptions' => ['class' => 'btn btn-primary', 'title' => YII::t('app', 'Choose image')],
                'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => YII::t('app', 'Clear')],
                'multiple'      => false,       // возможность выбора нескольких файлов
                'name'       => StringHelper::basename(get_class($model)) . '[image]',
                'value'      => $model->image,
            ]); ?>
        </div>
    </div>
    <div class="col-md-8 col-sm-6">
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'model')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'ean')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'upc')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'jan')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'mpn')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4 col-sm-6">
        <?= $form->field($model, 'alias')->textInput(['class' => 'form-control seodest-input', 'maxlength' => true]) ?>
        <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'points')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'pcolor_id')->widget(Select2::classname(), [
            'data' => $model->colorsList,
            'theme' => Select2::THEME_DEFAULT,
            'language' => 'ru',
            'options' => ['placeholder' => 'Выберите цвет', 'multiple' => false, 'autocomplete' => 'off'],
            'pluginOptions' => [
                'templateResult' => new JsExpression('format'),
                'templateSelection' => new JsExpression('format'),
                'escapeMarkup' => $escape,
                'allowClear' => true
            ],
        ]); ?>
    </div>
    <div class="col-md-4 col-sm-6">
        <?= $form->field($model, 'quantity')->textInput(['type' => 'number']) ?>
        <?= $form->field($model, 'minimum')->textInput(['type' => 'number']) ?>
        <?= $form->field($model, 'stock_status_id')->dropDownList($model->stockStatusesList) ?>
        <?= $form->field($model, 'shipping')->checkbox() ?>
        <?= $form->field($model, 'subtract')->checkbox() ?>
    </div>
    <div class="col-md-4 col-sm-6">
        <?= $form->field($model, 'manufacturer_id')->dropDownList($model->manufacturersList) ?>
        <?= $form->field($model, 'dateavailable')->widget(DateTimePicker::classname(), [
          'language' => 'ru',
          'size' => 'ms',
          'template' => '{input}{reset}{button}',
          'pickButtonIcon' => 'glyphicon glyphicon-time',
          'clientOptions' => [
            'autoclose' => true,
            'format' => 'dd.mm.yyyy hh:ii',
            'todayBtn' => true
          ],
        ]); ?>
        <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']); ?>
        <?= $form->field($model, 'status')->checkbox() ?>
    </div>
</div>
