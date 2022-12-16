<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

use common\modules\blog\models\BlogCategory;
use common\modules\blog\Module;
use common\modules\blog\models\BlogPost;
//use kartik\markdown\MarkdownEditor;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use backend\components\InputFileWithPic;

/* @var $this yii\web\View */
/* @var $model backend\modules\blog\models\BlogPost */
/* @var $form yii\widgets\ActiveForm */

$editor_options = ElFinder::ckeditorOptions(['elfinder', 'path' => 'image']);
$editor_options['preset'] = 'standart';
$editor_options['height'] = 400;
$editor_options['extraPlugins'] = 'basewidget,layoutmanager,triggers';
$editor_options['layoutmanager_loadbootstrap'] = true;
?>

<div class="blog-post-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-12\">{input}{error}</div>",
            'labelOptions' => ['class' => 'col-lg-12'],
        ],
    ]); ?>
    <?= $form->errorSummary($model); ?>
    <?= $form->field($model, 'category_id')->dropDownList(ArrayHelper::map(BlogCategory::get(0, BlogCategory::find()->all()), 'id', 'str_label')) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'brief')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'content')->widget(CKEditor::className(),[
        'editorOptions' => $editor_options,
    ]); ?>

    <div class="row">
        <div class="col-sm-9">
            <?= $form->field($model, 'tags')->textInput(['maxlength' => 128]) ?>

            <?= $form->field($model, 'slug')->textInput(['maxlength' => 128]) ?>

            <?= $form->field($model, 'click')->textInput() ?>

            <?= $form->field($model, 'likes')->textInput() ?>

            <?= $form->field($model, 'status')->dropDownList(BlogPost::getStatusList()) ?>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <div class="picture-div">
                    <label class="control-label">Фото</label>
                    <?= InputFileWithPic::widget([
                        'language'     => 'ru',
                        'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                        'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
                        'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                        'template'      => '<div class="picture">{picture}</div>{input}<div class="btn-group">{browse_button}{clear_button}</div>',
                        'options'       => ['class' => 'form-control', 'id' => 'BlogPost-banner'],
                        'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
                        'clearButtonName'    => '<span class="glyphicon glyphicon-erase"></span>',
                        'buttonOptions' => ['class' => 'btn btn-primary', 'title' => 'Выбрать изображение'],
                        'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => 'Очистить'],
                        'multiple'      => false,       // возможность выбора нескольких файлов
                        'name'       => 'BlogPost[banner]',
                        'value'      => $model->banner,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?= Html::submitButton($model->isNewRecord ? Module::t('blog', 'Create') : Module::t('blog', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
