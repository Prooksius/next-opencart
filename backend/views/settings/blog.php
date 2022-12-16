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

<?= MyHtml::formGroup('blog', 'meta_title', 'Title страницы', $blog['meta_title'])?>
<?= MyHtml::formGroup('blog', 'meta_desc', 'Description страницы', $blog['meta_desc'])?>
<?= MyHtml::formGroup('blog', 'meta_keywords', 'Keywords страницы', $blog['meta_keywords'])?>
<?= MyHtml::formGroup('blog', 'h1', 'H1 страницы', $blog['h1'])?>
<?= MyHtml::formGroup('blog', 'h2', 'H2 страницы', $blog['h2'])?>
<?= Html::tag('div', Html::label('Анонс страницы:') . Html::textarea('blog[anons]', $blog['anons'], ['class' => 'form-control']), ['class' => 'form-group'])?>
<?= Html::tag('div', Html::label('СЕО-текст страницы:'). CKEditor::widget(['name' => 'blog[text]', 'value' => $blog['text']]), ['class' => 'form-group'])?>
