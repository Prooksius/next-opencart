<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\InputFileWithPic;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reviews-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="form-group">
                <div class="picture-div">
                    <label class="control-label">Фото</label>
                    <?= InputFileWithPic::widget([
                        'language'     => 'ru',
                        'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                        'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
                        'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                        'template'      => '<div class="picture">{picture}</div>{input}<div class="btn-group">{browse_button}{clear_button}</div>',
                        'options'       => ['class' => 'form-control', 'id' => 'Reviews-photo'],
                        'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
                        'clearButtonName'    => '<span class="glyphicon glyphicon-erase"></span>',
                        'buttonOptions' => ['class' => 'btn btn-primary', 'title' => 'Выбрать изображение'],
                        'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => 'Очистить'],
                        'multiple'      => false,       // возможность выбора нескольких файлов
                        'name'       => 'Reviews[photo]',
                        'value'      => $model->photo,
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="col-lg-10 col-md-9 col-sm-6">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                     <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                     <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
                     <?= $form->field($model, 'info_type')->dropDownList([0 => 'Картинка + текст', 1 => 'Картинка + видео']) ?>
                </div>
                <div class="col-md-6 col-sm-12">
                    <?= $form->field($model, 'deposit')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'period')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'income')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="form-group">
                        <div class="picture-div">
                                <label class="control-label">Картинка</label>
                            <?= InputFileWithPic::widget([
                                'language'     => 'ru',
                                'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                                'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
                                'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                                'template'      => '<div class="picture">{picture}</div>{input}<div class="btn-group">{browse_button}{clear_button}</div>',
                                'options'       => ['class' => 'form-control', 'id' => 'Reviews-picture'],
                                'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
                                'clearButtonName'    => '<span class="glyphicon glyphicon-erase"></span>',
                                'buttonOptions' => ['class' => 'btn btn-primary', 'title' => 'Выбрать изображение'],
                                'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => 'Очистить'],
                                'multiple'      => false,       // возможность выбора нескольких файлов
                                'name'       => 'Reviews[picture]',
                                'value'      => $model->picture,
                            ]); ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-10 col-md-9 col-sm-6">
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                    <?= $form->field($model, 'video')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <?= $form->field($model, 'status')->checkbox(); ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Отмена', Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
