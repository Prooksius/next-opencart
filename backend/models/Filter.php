<?php

namespace app\models;

use common\models\Filter as ModelsFilter;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "filter".
 */
class Filter extends ModelsFilter
{
  private static $_all_filters;

  public $name; 
  public $group_name; 

  public static function getAllFilters()
  {
    if (!self::$_all_filters) {
      self::$_all_filters = ArrayHelper::map(
        self::find()
          ->alias('f')
          ->select([
            'f.id', 
            'fgd.name AS group_name',
            'fd.name AS name'
          ])
          ->leftJoin('oc_filter_description fd', '(fd.filter_id = f.id AND fd.language_id = "' . \Yii::$app->language . '")')          
          ->leftJoin('oc_filter_group_description fgd', '(fgd.filter_group_id = f.filter_group_id AND fgd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('fgd.name ASC, fd.name ASC')
          ->all(),
        'id', 'name', 'group_name');
    }
    return self::$_all_filters;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'filter_group_id' => YII::t('filter', 'Filter Group'),
      'sort_order' => Yii::t('app', 'Sort order'),
      'filter_sort_order' => Yii::t('app', 'Filter sort order'),
      'alias' => Yii::t('app', 'Alias'),
      'product_show' => Yii::t('app', 'Shown in product?'),
      'open_filter' => Yii::t('app', 'Open in filter?'),
      'icon' => Yii::t('app', 'Icon'),
    ];
  }
}
