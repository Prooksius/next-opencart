<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "language".
 */
class FilterGroup extends MyActiveRecord
{

  protected $_desc_class = '\common\models\FilterGroupDesc';
  protected $_desc_id_name = 'filter_group_id';
  protected $_desc_fields = ['name', 'description'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_filter_group';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['alias'], 'required'],
      [['sort_order', 'filter_sort_order', 'open_filter', 'product_show'], 'integer'],
      [['alias'], 'string', 'max' => 100],
      [['icon'], 'string', 'max' => 255],
      [['languages'], 'safe'],
    ];
  }
}
