<?php

use yii\helpers\Html;
use backend\components\MyHtml;

/* @var $this yii\web\View */
/* @var $settings array */

?>
<?= MyHtml::formLangGroup('mainpage', 'title', YII::t('app', 'Title'), $main['title'][$locale], $locale)?>

<?= MyHtml::formLangGroup('mainpage', 'meta_title', YII::t('app', 'META Title'), $main['meta_title'][$locale], $locale)?>
<?= Html::tag('div', Html::label(YII::t('app', 'META Desctiption')) . Html::textarea('mainpage[meta_desc]['.$locale.']', $main['meta_desc'][$locale], ['class' => 'form-control', 'rows' => 5]), ['class' => 'form-group'])?>
<?= MyHtml::formLangGroup('mainpage', 'meta_keywords', YII::t('app', 'META Keywords'), $main['meta_keywords'][$locale], $locale)?>
