<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use backend\components\InputFileGallery;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $settings array */

$jscript = "
        $(window).on('resize', function (e) {
            $('#main-slider-images').css('height', 'auto');
            setTimeout(function(){
                $('#main-slider-images').height($('#main-slider-images').height());
            }, 200);
        })
";

$this->registerJs( $jscript, View::POS_READY);

?>
<div class="row">
    <div class="col-md-6">
        <?= MyHtml::formGroup('general', 'sitename', 'Название сайта', $settings['sitename'])?>
    </div>
    <div class="col-md-6">
        <?= MyHtml::formGroup('general', 'phone', 'Телефон на сайте', $settings['phone'])?>
        <?= MyHtml::formGroup('general', 'email', 'Email', $settings['email'])?>
    </div>
</div>
<?= InputFileGallery::widget([
	'id' => 'footer-icons',
	'label' => 'Иконки в футере. Для изменения порядка доступно перетаскивание мышкой',
	'name' => 'general[footer_icons]',
	'value' => $settings['footer_icons'],
	'language'   => 'ru',
	'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
	'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
	'filter'     => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
	'template'      => '<div class="picture">{picture}</div>{input}<div class="btn-group">{browse_button}{delete_button}</div>',
	'options'       => ['id' => 'footer-icons'],
	'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
	'clearButtonName'    => '<span class="glyphicon glyphicon-erase"></span>',
	'deleteButtonName'    => '<span class="glyphicon glyphicon-trash"></span>',
	'buttonOptions' => ['class' => 'btn btn-primary', 'title' => 'Выбрать изображение'],
	'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => 'Очистить'],
	'deleteButtonOptions' => ['class' => 'btn btn-danger', 'title' => 'Удалить'],
	'addButtonOptions' => ['class' => 'btn btn-primary', 'title' => 'Добавить изображение'],
	'multiple'          => false,       // возможность выбора нескольких файлов
])?>
