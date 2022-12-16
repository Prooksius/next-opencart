<?php

use yii\helpers\Html;
use \backend\components\InputFileWithPic;
use backend\components\MyHtml;

/* @var $this yii\web\View */
/* @var $model app\models\Pages */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="row">
    <div class="col-md-3 col-sm-4">
        <div class="picture-div">
            <label class="control-label"><?= $model->getAttributeLabel('flag') ?></label>
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
                'name'       => 'Country[flag]',
                'value'      => $model->flag,
            ]); ?>
        </div>
    </div>
    <div class="col-md-9 col-sm-8">
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'iso_code_2')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'iso_code_3')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'popularity')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'phonemask')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
</div>
