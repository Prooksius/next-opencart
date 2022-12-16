<?php

use backend\components\MyHtml;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Language */
/* @var $form yii\widgets\ActiveForm */

$taxTypeList = [
  0 => 'Без НДС',
  1 => 'НДС по ставке 0%',
  2 => 'НДС чека по ставке 10%',
  3 => 'НДС чека по ставке 18%',
  4 => 'НДС чека по расчетной ставке 10/110',
  5 => 'НДС чека по расчетной ставке 10/118',
  6 => 'НДС чека по расчетной ставке 20%',
  7 => 'НДС чека по расчетной ставке 20/120',
];

$taxSystemList = [
  0 => 'Общая',
  1 => 'Упрощенная, доход',
  2 => 'Упрощенная, доход минус расход',
  3 => 'Eдиный налог на вменённый доход',
  4 => 'Единый сельскохозяйственный налог',
  5 => 'Патентная система налогообложения',
];

$FFDVersionlist = [
  'v10' => '1.00',
  'v105' => '1.05',
//  'v11' => '1.1',
];

$paymentMethodTypeList = [
  1 => 'Полная предварительная оплата до момента передачи предмета расчёта',
  2 => 'Частичная предварительная оплата до момента передачи предмета расчёта',
  3 => 'Аванс',
  4 => 'Полная оплата в момент передачи предмета расчёта',
  5 => 'Частичная оплата предмета расчёта в момент его передачи с последующей оплатой в кредит',
  6 => 'Передача предмета расчёта без его оплаты в момент его передачи с последующей оплатой в кредит',
  7 => 'Оплата предмета расчёта после его передачи с оплатой в кредит',
];

$paymentObjectTypeList = [
  1 => 'Товар',
  2 => 'Подакцизный товар',
  3 => 'Работа',
  4 => 'Услуга',
  5 => 'Ставка азартной игры',
//  6 => 'Выигрыш азартной игры',
  7 => 'Лотерейный билет',
//  8 => 'Выигрыш лотереи',
  9 => 'Предоставление РИД',
  10 => 'Платёж',
//  11 => 'Агентское вознаграждение',
  12 => 'Составной предмет расчёта',
  13 => 'Иной предмет расчёта',
];
?>

<div class="total-form">

  <?php $form = ActiveForm::begin(); ?>

  <div class="row">
    <div class="col-md-4">
      <?= MyHtml::formGroup('ModuleGroup', 'name', Yii::t('app', 'Name'), $model->name)?>
    </div>
    <div class="col-md-4">
      <?= MyHtml::formGroup('ModuleGroup', 'sort_order', Yii::t('app', 'Sort order'), $model->sort_order)?>
    </div>
    <div class="col-md-4">
      <br />
      <br />
      <?= MyHtml::formGroupCheckbox('ModuleGroup', 'status', Yii::t('app', 'Active?'), 1, $model->status) ?>
    </div>
  </div>
  <hr>

  <div class="row">
    <div class="col-md-6">
      <?= MyHtml::formGroupLang('ModuleGroup', 'settingsArr[title]', 'Заголовок', $model->settingsArr['title'], [], $model->languagesList)?>
    </div>
    <div class="col-md-6">
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[order_status]', 'Статус заказа после оплаты', $model->settingsArr['order_status'], $model->orderStatusesList) ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <?= MyHtml::formGroup('ModuleGroup', 'settingsArr[merchantLogin]', 'Логин мерчанта', $model->settingsArr['merchantLogin'])?>
      <?= MyHtml::formGroup('ModuleGroup', 'settingsArr[merchantPassword]', 'Пароль мерчанта', $model->settingsArr['merchantPassword'])?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[logging]', 'Логирование', $model->settingsArr['logging'], [
        0 => 'Отключено', 
        1 => 'Включено'
      ])?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[ofd_status]', 'Фискализация', $model->settingsArr['ofd_status'], [
        0 => 'Не передавать данные корзины покупателя', 
        1 => 'Передавать данные корзины покупателя'
      ])?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[taxType]', 'Ставка НДС', $model->settingsArr['taxType'], 
        $taxTypeList
      )?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[paymentMethodType]', 'Тип оплаты', $model->settingsArr['paymentMethodType'], 
        $paymentMethodTypeList
      )?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[paymentObjectType]', 'Тип оплачиваемой позиции', $model->settingsArr['paymentObjectType'], 
        $paymentObjectTypeList
      )?>
    </div>
    <div class="col-md-6">
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[mode]', 'Режим работы', $model->settingsArr['mode'], [
        'test' => 'Тестовый', 
        'prod' => 'Боевой'
      ])?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[stage]', 'Стадийность платежа', $model->settingsArr['stage'], [
        'one' => 'Одностадиный', 
        'two' => 'Двухстадийный'
      ]) ?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[currency]', 'Валюта', $model->settingsArr['currency'], 
        $rbsCurrenciesList
      ) ?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[taxSystem]', 'Система налогообложения', $model->settingsArr['taxSystem'], 
        $taxSystemList
      ) ?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[ffdVersion]', 'Формат фискальных документов', $model->settingsArr['ffdVersion'], 
        $FFDVersionlist
      ) ?>
      <?= MyHtml::formGroupSelect('ModuleGroup', 'settingsArr[deliveryPaymentMethodType]', 'Тип оплаты для доставки', $model->settingsArr['deliveryPaymentMethodType'], 
        $paymentMethodTypeList
      ) ?>
    </div>
  </div>
  <div class="form-group">
    <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
