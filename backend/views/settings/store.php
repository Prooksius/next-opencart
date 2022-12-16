<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $settings array */

?>
<div class="row">
  <div class="col-md-4">
    <?= MyHtml::formGroupSelect('general', 'customer_group_id', 'Группа пользователя по умолчанию', $settings['customer_group_id'], $customerGroupsList)?>
    <?= MyHtml::formGroupSelect('general', 'currency', 'Валюта по умолчанию', $settings['currency'], $currenciesList)?>
    <?= MyHtml::formGroupSelect('general', 'language', 'Язык по умолчанию', $settings['language'], $languagesList)?>
    <?= MyHtml::formGroupSelect('general', 'weight_class_id', 'Единица веса по умолчанию', $settings['weight_class_id'], $weightClassesList)?>
    <?= MyHtml::formGroupSelect('general', 'length_class_id', 'Единица длины по умолчанию', $settings['length_class_id'], $lengthClassesList)?>
    <?= MyHtml::formGroupSelect('general', 'order_status_id', 'Статус заказа умолчанию', $settings['order_status_id'], $orderStatusesList)?>
    <?= MyHtml::formGroupSelect('general', 'show_zero_quantity', 'Показывать товары с нулевым количеством', $settings['show_zero_quantity'], [0 => 'Нет', 1 => 'Да']) ?>
    <?= MyHtml::formGroup('general', 'cart_preserve', 'Время хранения корзины, ч.', $settings['cart_preserve']) ?>
  </div>
  <div class="col-md-4">
    <h4>Выводить в фильтре</h4>
    <?= MyHtml::formGroupCheckbox('general', 'filter_show_price', 'Цены', 1, (int)$settings['filter_show_price']) ?>
    <?= MyHtml::formGroupCheckbox('general', 'filter_show_brands', 'Бренды', 1, (int)$settings['filter_show_brands']) ?>
    <?= MyHtml::formGroupCheckbox('general', 'filter_show_attributes', 'Атрибуты', 1, (int)$settings['filter_show_attributes']) ?>
    <?= MyHtml::formGroupCheckbox('general', 'filter_show_options', 'Опции', 1, (int)$settings['filter_show_options']) ?>
    <?= MyHtml::formGroupCheckbox('general', 'filter_show_filters', 'Фильтры', 1, (int)$settings['filter_show_filters']) ?>
    <?= MyHtml::formGroupCheckbox('general', 'filter_show_colors', 'Цвета', 1, (int)$settings['filter_show_colors']) ?>
    <?= MyHtml::formGroupCheckbox('general', 'filter_brands_open', 'Аккордеон брендов открыт?', 1, (int)$settings['filter_brands_open']) ?>
    <?= MyHtml::formGroup('general', 'filter_brands_sort_order', 'Порядок сортировки брендов', $settings['filter_brands_sort_order'])?>
    <?= MyHtml::formGroupCheckbox('general', 'filter_colors_open', 'Аккордеон цветов открыт?', 1, (int)$settings['filter_colors_open']) ?>
    <?= MyHtml::formGroup('general', 'filter_colors_sort_order', 'Порядок сортировки брендов', $settings['filter_colors_sort_order'])?>
  </div>
  <div class="col-md-4">
    <h4>Размеры миниатюр</h4>
    <div class="row">
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_cart_width', 'Корзина (ширина)', $settings['thumbs_cart_width'])?>
      </div>
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_cart_height', 'Корзина (высота)', $settings['thumbs_cart_height'])?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_catalog_width', 'Каталог (ширина)', $settings['thumbs_catalog_width'])?>
      </div>
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_catalog_height', 'Каталог (высота)', $settings['thumbs_catalog_height'])?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_product_big_width', 'Товар - основная (ширина)', $settings['thumbs_product_big_width'])?>
      </div>
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_product_big_height', 'Товар - основная (высота)', $settings['thumbs_product_big_height'])?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_product_gallery_width', 'Товар - галерея (ширина)', $settings['thumbs_product_gallery_width'])?>
      </div>
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_product_gallery_height', 'Товар - галерея (высота)', $settings['thumbs_product_gallery_height'])?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_blog_width', 'Блог (ширина)', $settings['thumbs_blog_width'])?>
      </div>
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_blog_height', 'Блог (высота)', $settings['thumbs_blog_height'])?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_article_big_width', 'Статья блога - основная (ширина)', $settings['thumbs_article_big_width'])?>
      </div>
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_article_big_height', 'Статья блога - основная (высота)', $settings['thumbs_article_big_height'])?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_article_gallery_width', 'Статья блога - галерея (ширина)', $settings['thumbs_article_gallery_width'])?>
      </div>
      <div class="col-sm-6">
        <?= MyHtml::formGroup('general', 'thumbs_article_gallery_height', 'Статья блога - галерея (высота)', $settings['thumbs_article_gallery_height'])?>
      </div>
    </div>
  </div>
</div>