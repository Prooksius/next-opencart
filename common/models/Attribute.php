<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "language".
 */
class Attribute extends MyActiveRecord
{
   const VALUE_TYPE_CHECKBOX = 0;
   const VALUE_TYPE_RANGE = 1;
   const VALUE_TYPE_RADIO = 2;

   const VALUE_SORT_NUMBER = 0;
   const VALUE_SORT_STRING = 1;

  protected $_desc_class = '\common\models\AttributeDesc';
  protected $_desc_id_name = 'attribute_id';
  protected $_desc_fields = ['name', 'description'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_attribute';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['alias', 'attribute_group_id'], 'required'],
      [['attribute_group_id', 'sort_order', 'filter_sort_order', 'value_type', 'value_sort', 'show_filter', 'show_product', 'show_product_tile', 'open_filter'], 'integer'],
      ['value_type', 'in', 'range' => [self::VALUE_TYPE_CHECKBOX, self::VALUE_TYPE_RANGE, self::VALUE_TYPE_RADIO]],
      ['value_sort', 'in', 'range' => [self::VALUE_SORT_NUMBER, self::VALUE_SORT_STRING]],
      [['alias'], 'string', 'max' => 100],
      [['icon'], 'string', 'max' => 255],
      [['languages'], 'safe'],
    ];
  }
}
