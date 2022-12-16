<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "language".
 */
class OptionValue extends MyActiveRecord
{

  protected $_desc_class = '\common\models\OptionValueDesc';
  protected $_desc_id_name = 'option_value_id';
  protected $_desc_fields = ['name'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_option_value';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['alias', 'option_id'], 'required'],
      [['option_id', 'sort_order'], 'integer'],
      [['alias'], 'string', 'max' => 100],
      [['image'], 'string', 'max' => 255],
      [['color'], 'string', 'max' => 10],
      [['languages'], 'safe'],
    ];
  }
}
