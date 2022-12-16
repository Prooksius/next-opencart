<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

/* @var $this yii\web\View */
/* @var $settings array */

$editor_options = ElFinder::ckeditorOptions(['elfinder', 'path' => 'image']);
$editor_options['preset'] = '';
$editor_options['height'] = 400;
$editor_options['language'] = explode('-', \Yii::$app->language)[0];
$editor_options['extraPlugins'] = 'basewidget,layoutmanager,triggers,youtube';
$editor_options['layoutmanager_loadbootstrap'] = true;

?>
<?= Html::tag(
  'div', 
  Html::label(YII::t('html', 'Html Content')) . 
    CKEditor::widget(['name' => 'Module[settingsArr][html]['.$locale.']', 'value' => $model->settingsArr['html'][$locale], 'editorOptions' => $editor_options]), 
   ['class' => 'form-group']
) ?>
