<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "oc_option".
 */
class Option extends MyActiveRecord
{

  const TYPE_SELECT = 'select';
  const TYPE_RADIO = 'radio';
  const TYPE_CHECKBOX = 'checkbox';
  const TYPE_TEXT = 'text';
  const TYPE_TEXTAREA = 'textarea';
  const TYPE_DATE = 'date';
  const TYPE_TIME = 'time';
  const TYPE_DATETIME = 'datetime';

  protected $_desc_class = '\common\models\OptionDesc';
  protected $_desc_id_name = 'option_id';
  protected $_desc_fields = ['name', 'description'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_option';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['sort_order', 'filter_sort_order', 'open_filter'], 'integer'],
      [['type'], 'string', 'max' => 32],
      ['type', 'in', 'range' => [
        self::TYPE_SELECT, 
        self::TYPE_RADIO, 
        self::TYPE_CHECKBOX, 
        self::TYPE_TEXT, 
        self::TYPE_TEXTAREA, 
        self::TYPE_DATE, 
        self::TYPE_TIME, 
        self::TYPE_DATETIME
      ]],
      [['alias'], 'string', 'max' => 100],
      [['languages'], 'safe'],
    ];
  }
}
