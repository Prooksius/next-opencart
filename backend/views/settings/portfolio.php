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

<?= MyHtml::formGroup('portfolio', 'meta_title', 'Title страницы', $portfolio['meta_title'])?>
<?= MyHtml::formGroup('portfolio', 'meta_desc', 'Description страницы', $portfolio['meta_desc'])?>
<?= MyHtml::formGroup('portfolio', 'h1', 'H1 страницы', $portfolio['h1'])?>
<?= MyHtml::formGroup('portfolio', 'h2', 'H2 страницы', $portfolio['h2'])?>
<?= Html::tag('div', Html::label('Анонс страницы:') . Html::textarea('portfolio[anons]', $portfolio['anons'], ['class' => 'form-control']), ['class' => 'form-group'])?>
<?= Html::tag('div', Html::label('СЕО-текст страницы:'). CKEditor::widget(['name' => 'portfolio[text]', 'value' => $portfolio['text']]), ['class' => 'form-group'])?>
