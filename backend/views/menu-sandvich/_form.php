<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;
use backend\components\InputFileWithPic;

/* @var $this yii\web\View */
/* @var $model app\models\Menu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="picture-div left-align grey-bg">
            <label class="control-label">Иконка</label>
            <?= InputFileWithPic::widget([
                'language'     => 'ru',
                'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
                'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                'template'      => '<div class="picture">{picture}</div>{input}<div class="btn-group">{browse_button}{clear_button}</div>',
                'options'       => ['class' => 'form-control', 'id' => 'Service-picture'],
                'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
                'clearButtonName'    => '<span class="glyphicon glyphicon-erase"></span>',
                'buttonOptions' => ['class' => 'btn btn-primary', 'title' => 'Выбрать изображение'],
                'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => 'Очистить'],
                'multiple'      => false,       // возможность выбора нескольких файлов
                'name'       => 'Menu[icon]',
                'value'      => $model->icon,
            ]); ?>
        </div>
    </div>

    <?= $form->field($model, 'status')->checkbox(); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
