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

<?= Html::tag('div', Html::label(YII::t('app', 'SEO Text')) . CKEditor::widget(['name' => 'partner[seo_text]['.$locale.']', 'value' => $partner['seo_text'][$locale], 'editorOptions' => $editor_options]), ['class' => 'form-group']);?>
