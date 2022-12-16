<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "language".
 */
class Filter extends MyActiveRecord
{

  protected $_desc_class = '\common\models\FilterDesc';
  protected $_desc_id_name = 'filter_id';
  protected $_desc_fields = ['name'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_filter';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['alias', 'filter_group_id'], 'required'],
      [['filter_group_id', 'sort_order'], 'integer'],
      [['alias'], 'string', 'max' => 100],
      [['image'], 'string', 'max' => 255],
      [['languages'], 'safe'],
    ];
  }
}
