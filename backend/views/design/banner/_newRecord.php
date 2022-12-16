<?php

use yii\helpers\Html;
use \backend\components\InputFileWithPic;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

?>
<tr id="images-record-<?= $language ?>-<?= $key ?>">
  <td>
    <?= Html::input(
      'text', 
      StringHelper::basename(get_class($model)) . '[images]['.$language.']['.$key.'][title]', 
      $image['title'],
      ['class' => 'form-control', 'placeholder' => YII::t('banner', 'Title')])
    ?>
    <?= Html::input(
      'text', 
      StringHelper::basename(get_class($model)) . '[images]['.$language.']['.$key.'][text1]', 
      $image['text1'],
      ['class' => 'form-control', 'placeholder' => YII::t('banner', 'Text1')])
    ?>
    <?= Html::input(
      'text', 
      StringHelper::basename(get_class($model)) . '[images]['.$language.']['.$key.'][text2]', 
      $image['text2'],
      ['class' => 'form-control', 'placeholder' => YII::t('banner', 'Text2')])
    ?>
    <?= Html::input(
      'text', 
      StringHelper::basename(get_class($model)) . '[images]['.$language.']['.$key.'][text3]', 
      $image['text3'],
      ['class' => 'form-control', 'placeholder' => YII::t('banner', 'Text3')])
    ?>
  </td>
  <td>
    <?= Html::input(
      'text', 
      StringHelper::basename(get_class($model)) . '[images]['.$language.']['.$key.'][link]', 
      $image['link'],
      ['class' => 'form-control', 'placeholder' => YII::t('banner', 'Link')])
    ?>
  </td>
  <td>
    <div class="picture-div table-picture">
      <?= InputFileWithPic::widget([
          'language'      => 'ru',
          'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
          'path'          => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
          'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
          'template'      => '<div class="picture">{picture}</div>{input}<div class="btn-group">{browse_button}{clear_button}</div>',
          'options'       => ['class' => 'form-control', 'id' => 'pages-image-' . $language . '-' . $key],
          'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
          'clearButtonName' => '<span class="glyphicon glyphicon-erase"></span>',
          'buttonOptions' => ['class' => 'btn btn-primary', 'title' => YII::t('app', 'Choose image')],
          'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => YII::t('app', 'Clear')],
          'multiple'      => false,       // возможность выбора нескольких файлов
          'name'          => StringHelper::basename(get_class($model)) . '[images]['.$language.']['.$key.'][image]',
          'value'         => $image['image'],
      ]); ?>
    </div>
  </td>
  <td>
    <?= Html::input(
      'text', 
      StringHelper::basename(get_class($model)) . '[images]['.$language.']['.$key.'][sort_order]', 
      $image['sort_order'],
      ['class' => 'form-control', 'placeholder' => YII::t('app', 'Sort order')])
    ?>
  </td>
  <td style="width: 1px"><button type="button" class="btn btn-danger" onclick="$(this).closest('tr').remove();" title="<?= YII::t('banner', 'Delete banner') ?>">&times;</button></td>
</tr>
