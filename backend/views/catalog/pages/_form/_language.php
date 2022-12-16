<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

?>
<?= Tabs::widget([
    'items' => MyHtml::languageTabs($model, $languages, [
        'name' => 'text',
        'title' => 'text',
        'subtitle' => 'textarea',
        'text' => 'CKEditor_big',
        'page_title' => 'text',
        'page_desc' => 'textarea',
        'page_kwords' => 'textarea',
    ]),
    'options' => [
        'id' => 'Speciality-update-tabs',
    ]
]);?>