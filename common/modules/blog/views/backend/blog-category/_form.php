<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

use common\modules\blog\models\BlogCategory;
use common\modules\blog\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\InputFileWithPic;

/* @var $this yii\web\View */
/* @var $model akiraz2\blog\models\BlogCategory */
/* @var $form yii\widgets\ActiveForm */

//fix the issue that it can assign itself as parent
$parentCategory = ArrayHelper::merge([0 => Module::t('blog', 'Root Category')], ArrayHelper::map(BlogCategory::get(0, BlogCategory::find()->all()), 'id', 'str_label'));
unset($parentCategory[$model->id]);

?>

<div class="blog-category-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-9\">{input}{error}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-sm-9">
        <?= $form->field($model, 'parent_id')->dropDownList($parentCategory) ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

        <?= $form->field($model, 'slug')->textInput(['maxlength' => 128]) ?>

        <?= $form->field($model, 'is_nav')->dropDownList(BlogCategory::getArrayIsNav()) ?>

        <?= $form->field($model, 'sort_order')->textInput() ?>

        <?= $form->field($model, 'page_size')->textInput() ?>

        <?= $form->field($model, 'template')->textInput(['maxlength' => 255]) ?>

        <?= $form->field($model, 'redirect_url')->textInput(['maxlength' => 255]) ?>

        <?= $form->field($model, 'status')->dropDownList(BlogCategory::getStatusList()) ?>
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
                        'options'       => ['class' => 'form-control', 'id' => 'BlogCategory-banner'],
                        'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
                        'clearButtonName'    => '<span class="glyphicon glyphicon-erase"></span>',
                        'buttonOptions' => ['class' => 'btn btn-primary', 'title' => 'Выбрать изображение'],
                        'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => 'Очистить'],
                        'multiple'      => false,       // возможность выбора нескольких файлов
                        'name'       => 'BlogCategory[banner]',
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
