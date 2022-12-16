<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 07.05.2020
 * Time: 20:29
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Редактор файлов темы - редактирование файла';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="theme-edit">
    <div class="row">
        <div class="col-sm-12">
            <?php $form = ActiveForm::begin(); ?>
            <h4><?= $model->filetitle; ?></h4>
            <?= $form->field($model, 'filecontent')->textarea(['rows' => 30]); ?>

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
            <?= Html::a('Отмена', Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
