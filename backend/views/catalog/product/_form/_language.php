<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

?>
<?= Tabs::widget([
  'items' => MyHtml::languageTabs($model, $model->languagesList, [
    'name' => 'text',
    'short_name' => 'text',
    'description' => 'CKEditor',
    'meta_title' => 'text',
    'meta_h1' => 'text',
    'meta_description' => 'textarea',
    'meta_keyword' => 'textarea',
    'tag' => 'text',
  ]),
  'options' => [
    'id' => 'manufacturer-update-tabs',
  ]
]);?>