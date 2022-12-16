<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "oc_manufacturer".
 */
class Manufacturer extends \yii\db\ActiveRecord
{

  public $name;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_manufacturer';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['sort_order'], 'integer'],
      [['alias', 'image'], 'string'],
    ];
  }

  public static function getAllManufacturers() 
  {
    $saved_items = Yii::$app->cache->getOrSet('manufacturer_list', function () {
      return self::find()
        ->alias('m')
        ->select([
          'm.alias',
          'md.name',
        ])
        ->leftJoin('oc_manufacturer_description md', 'md.manufacturer_id = m.id')
        ->where(['md.language_id' => \Yii::$app->language])
        ->orderBy(['md.name' => SORT_ASC])
        ->all();
    }, 3600);

    $items = [];

    foreach ($saved_items as $item) {
      $items[] = [
        'alias' => $item->alias,
        'name'  => $item->name,
      ];
    }

    return $items;

  }

  public static function getSelectedBrands($sel_brands)
  {
    $brands = [];
    foreach ($sel_brands as $item) {
      $brands[] = Yii::$app->db->quoteValue($item);
    }
    $sel_brands = implode(", ", $brands);

    $saved_items = self::find()
      ->alias('m')
      ->select([
        'm.*',
        'md.name AS name'
      ])
      ->leftJoin('oc_manufacturer_description md', 'md.manufacturer_id = m.id AND md.language_id = "' . \Yii::$app->language . '"')
      ->where('m.alias IN (' . $sel_brands . ')')
      ->all();

    $items = [];

    foreach ($saved_items as $item) {
      $items[$item->alias] = $item->name;
    }

    return $items;
  }

}
