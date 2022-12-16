<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "language".
 */
class AttributeGroup extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_attribute_group';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['sort_order'], 'integer'],
    ];
  }
}
