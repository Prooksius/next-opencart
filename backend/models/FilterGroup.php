<?php

namespace app\models;

use common\models\FilterGroup as ModelsFilterGroup;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "attribute_grou".
 */
class FilterGroup extends ModelsFilterGroup
{
  private static $_all_groups;

  public $name; 

  public static function getAllGroups()
  {
    if (!self::$_all_groups) {
      self::$_all_groups = ArrayHelper::map(
        self::find()
          ->alias('fg')
          ->select([
            'fg.id', 
            'fgd.name'
          ])
          ->leftJoin('oc_filter_group_description fgd', '(fgd.filter_group_id = fg.id AND fgd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('fgd.name ASC')
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
      'description' => YII::t('app', 'Description'),
      'alias' => YII::t('app', 'Alias'),
      'icon' => YII::t('app', 'Icon'),
      'product_show' => YII::t('app', 'Shown in product?'),
      'open_filter' => YII::t('app', 'Open in filter?'),
      'sort_order' => YII::t('app', 'Sort Order'),
      'filter_sort_order' => YII::t('app', 'Filter sort order'),
    ];
  }
}
