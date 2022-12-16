<?php

namespace common\models;

/**
 * This is the model class for table "oc_module_group".
 */
class ModuleGroup extends \yii\db\ActiveRecord
{

  public $instances;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_module_group';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['category', 'name', 'code', 'settings'], 'string'],
      [['sort_order', 'status', 'single'], 'integer'],
    ];
  }
}
