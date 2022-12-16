<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyCustomerSelect;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = 'Создать пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6 col-sm-6 login-pass-cont">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->label(($mode == 'create' ? 'Пароль' : 'Новый пароль')) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'clients')->widget(MyCustomerSelect::className(), [
                'id' => 'customer-select',
            ])->label('Клиенты') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
