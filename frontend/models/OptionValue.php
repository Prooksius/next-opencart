<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "oc_option_value".
 */
class OptionValue extends \yii\db\ActiveRecord
{

  public $name;

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
      [['option_id', 'sort_order'], 'integer'],
      [['alias', 'image', 'color'], 'string'],
    ];
  }

}
