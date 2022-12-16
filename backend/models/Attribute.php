<?php

namespace app\models;

use common\models\Attribute as ModelsAttribute;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "attribute".
 */
class Attribute extends ModelsAttribute
{
  private static $_all_attributes;

  public $name; 
  public $group_name; 

  public static function getAllAttributes()
  {
    if (!self::$_all_attributes) {
      self::$_all_attributes = ArrayHelper::map(
        self::find()
          ->alias('a')
          ->select([
            'a.id', 
            'agd.name AS group_name',
            'ad.name AS name'
          ])
          ->leftJoin('oc_attribute_description ad', '(ad.attribute_id = a.id AND ad.language_id = "' . \Yii::$app->language . '")')          
          ->leftJoin('oc_attribute_group_description agd', '(agd.attribute_group_id = a.attribute_group_id AND agd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('agd.name ASC, ad.name ASC')
          ->all(),
        'id', 'name', 'group_name');
    }
    return self::$_all_attributes;
  }

  public function getValueTypesList()
  {
    return [
      self::VALUE_TYPE_CHECKBOX => YII::t('app', 'Checkbox'),
      self::VALUE_TYPE_RANGE => YII::t('app', 'Range'),
      self::VALUE_TYPE_RADIO => YII::t('app', 'Radio'),
    ];
  }  

  public function getValueSortList()
  {
    return [
      self::VALUE_SORT_NUMBER => YII::t('app', 'Number'),
      self::VALUE_SORT_STRING => YII::t('app', 'String'),
    ];
  }  

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'group_name' => YII::t('attribute', 'Group name'),
      'description' => YII::t('attribute', 'Short description'),
      'attribute_group_id' => YII::t('attribute', 'Attribute Group'),
      'sort_order' => Yii::t('app', 'Sort order'),
      'filter_sort_order' => Yii::t('app', 'Filter sort order'),
      'alias' => Yii::t('app', 'Alias'),
      'value_type' => Yii::t('attribute', 'Value type'),
      'value_sort' => Yii::t('attribute', 'Value sort'),
      'show_filter' => Yii::t('app', 'Shown in filter?'),
      'show_product' => Yii::t('app', 'Shown in product?'),
      'show_product_tile' => Yii::t('app', 'Shown in product tile?'),
      'open_filter' => Yii::t('app', 'Open in filter?'),
      'icon' => Yii::t('app', 'Icon'),
    ];
  }
}
