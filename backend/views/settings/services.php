<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 05.06.2019
 * Time: 11:27
 */

use yii\helpers\Html;
use backend\components\MyHtml;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

?>

<?= MyHtml::formGroup('services', 'meta_title', 'Title страницы', $services['meta_title'])?>
<?= MyHtml::formGroup('services', 'meta_desc', 'Description страницы', $services['meta_desc'])?>
<?= MyHtml::formGroup('services', 'h1', 'H1 страницы', $services['h1'])?>
<?= MyHtml::formGroup('services', 'h2', 'H2 страницы', $services['h2'])?>
<?= Html::tag('div', Html::label('СЕО-текст страницы:'). CKEditor::widget(['name' => 'services[text]', 'value' => $services['text']]), ['class' => 'form-group'])?>
