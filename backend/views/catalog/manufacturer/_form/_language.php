<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

//var_dump($model->errors);

?>
<?= Tabs::widget([
  'items' => MyHtml::languageTabs($model, $languages, [
    'name' => 'text',
    'description' => 'CKEditor',
    'meta_title' => 'text',
    'meta_h1' => 'text',
    'meta_description' => 'textarea',
    'meta_keyword' => 'text',
  ]),
  'options' => [
    'id' => 'manufacturer-update-tabs',
  ]
]);?>