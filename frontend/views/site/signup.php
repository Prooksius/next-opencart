<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \frontend\models\SignupForm */

use frontend\components\ReCaptchaWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<div class="site-login">
  <ul class="popup-list">
    <li>
      <a class="active">Регистрация</a>
    </li>
    <li>
      <a class="inactive" data-target="/site/loginpopup">Вход</a>
    </li>
  </ul>
  <?php $form = ActiveForm::begin([
    'id' => 'form-signup',
    'enableAjaxValidation' => false,
    'options' => [
      'class' => 'popup-form',
    ]
  ]); ?>
  <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'input', 'placeholder' => 'Логин'])->label(false) ?>

  <?= $form->field($model, 'fio')->textInput(['class' => 'input', 'placeholder' => 'Имя'])->label(false) ?>

  <?= $form->field($model, 'email')->textInput(['class' => 'input', 'placeholder' => 'Email'])->label(false) ?>

  <?= $form->field($model, 'phone')->textInput(['type' => 'tel', 'class' => 'input', 'placeholder' => 'Телефон'])->label(false) ?>

  <?= $form->field($model, 'password')->passwordInput(['class' => 'input', 'placeholder' => 'Пароль'])->label(false) ?>

  <?= $form->field($model, 'passwordRepeat')->passwordInput(['class' => 'input', 'placeholder' => 'Повторите пароль'])->label(false) ?>

  <?= $form->field($model, 'ref_link')->textInput(['class' => 'input', 'placeholder' => 'Код партнера'])->label(false) ?>

  <script>
    //$(document).ready(function() {
    var trigger = 0;
    //}); // $(document).ready    
  </script>

  <div class="form-group">
    <?= Html::Button('Зарегистрироваться', ['class' => 'btn2 sitelogin-button', 'data-action' => 'register', 'data-link' => '/site/signup', 'onclick' => 'trigger = send_data_to_b24(trigger);']) ?>
  </div>
  <p class="privacy">Регистрируясь, вы даёте согласие на обработку <a href="#">персональных данных</a></p>

  <script>
    function send_data_to_b24(trigger) {
      return;

      let form_obj = document.getElementById('form-signup');
      let login = form_obj.elements['signupform-username'].value;
      let fio = form_obj.elements['signupform-fio'].value;
      let email = form_obj.elements['signupform-email'].value;
      let phone = form_obj.elements['signupform-phone'].value;
      let password = form_obj.elements['signupform-password'].value;
      let password_repeat = form_obj.elements['signupform-passwordrepeat'].value;
      let ref_link = form_obj.elements['signupform-ref_link'].value;

      //if (trigger == 0){
      if (login != '' && fio != '' && phone != '' && password != '' && password_repeat != '' && ref_link != '') {

        let post_url = 'https://copy-trade.ru/';
        let post_data = {
          'login': login,
          'fio': fio,
          'email': email,
          'phone': phone,
          'password': password,
          'password_repeat': password_repeat
        };

        $.post(post_url, post_data, function(data) {
          //alert(data);
        });

      }

      return 1;
      //}

    }
  </script>



  <?php ActiveForm::end(); ?>

</div>