<?php

namespace app\models;

use common\models\AttributeGroup as ModelsAttributeGroup;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "attribute_grou".
 */
class AttributeGroup extends ModelsAttributeGroup
{
  private static $_all_groups;

  public $name; 

  public static function getAllGroups()
  {
    if (!self::$_all_groups) {
      self::$_all_groups = ArrayHelper::map(
        self::find()
          ->alias('ag')
          ->select([
            'ag.id', 
            'agd.name'
          ])
          ->leftJoin('oc_attribute_group_description agd', '(agd.attribute_group_id = ag.id AND agd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('agd.name ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_groups;
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
