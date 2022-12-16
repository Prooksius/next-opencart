<?php

namespace app\models;

use common\models\CustomerGroup as ModelsCustomerGroup;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "attribute_grou".
 */
class CustomerGroup extends ModelsCustomerGroup
{
  private static $_all_groups;

  public $name; 

  public static function getAllGroups()
  {
    if (!self::$_all_groups) {
      self::$_all_groups = ArrayHelper::map(
        self::find()
          ->alias('cg')
          ->select([
            'cg.id', 
            'cgd.name'
          ])
          ->leftJoin('oc_customer_group_description cgd', '(cgd.customer_group_id = cg.id AND cgd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('cgd.name ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_groups;
  }

  public function isDefault()
  {
    return (int)Yii::$app->shopConfig->getParam('customer_group_id') == $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'sort_order' => YII::t('app', 'Sort Order'),
    ];
  }
}
