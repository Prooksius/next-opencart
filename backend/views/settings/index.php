<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;
use backend\components\InputFileGallery;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $settings array */

$this->title = 'Глобальные настройки сайта';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settings-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?= Tabs::widget([
        'items' => [
            [
                'label' => 'Настройки сайта',
                'content' => $this->render('general', [
                  'settings' => $settings, 
                  'form' => $form]),
                'active' => true,
            ],
            [
                'label' => 'Метатэги',
                'content' => $this->render('mainpage', ['mainpage' => $mainpage, 'form' => $form, 'languages' => $languages]),
            ],
            [
                'label' => 'Магазин',
                'content' => $this->render('store', [
                  'settings' => $settings, 
                  'customerGroupsList' => $customerGroupsList, 
                  'currenciesList' => $currenciesList, 
                  'languagesList' => $languagesList, 
                  'orderStatusesList' => $orderStatusesList, 
                  'weightClassesList' => $weightClassesList, 
                  'lengthClassesList' => $lengthClassesList, 
                  'form' => $form,
                ]),
            ],
            [
                'label' => 'Месседжеры',
                'content' => $this->render('social', ['social' => $social, 'form' => $form]),
            ],
            [
                'label' => 'Платежный шлюз',
                'content' => $this->render('merchant', ['merchant' => $merchant, 'form' => $form]),
            ],
            [
                'label' => 'Bitrix24',
                'content' => $this->render('bitrix24', ['bitrix24' => $bitrix24, 'form' => $form]),
            ],
            [
                'label' => 'Страницы',
                'content' => $this->render('pages', ['partner' => $partner, 'form' => $form, 'languages' => $languages]),
            ],
        ],
        'options' => [
            'id' => 'settings-update-tabs',
        ]
    ]); ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>