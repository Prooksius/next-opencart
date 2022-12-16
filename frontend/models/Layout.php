<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "oc_layout".
 */
class Layout extends \yii\db\ActiveRecord
{

  public $instances;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_layout';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id'], 'integer'],
      [['name', 'code'], 'string'],
      ['modules', 'safe'],
    ];
  }
}
