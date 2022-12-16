<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "oc_weight_class".
 */
class WeightClass extends MyActiveRecord
{

  protected $_desc_class = '\common\models\WeightClassDesc';
  protected $_desc_id_name = 'weight_class_id';
  protected $_desc_fields = ['title', 'unit'];

  public $title;
  public $unit;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_weight_class';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['value'], 'number'],
      [['languages'], 'safe'],
    ];
  }
}
