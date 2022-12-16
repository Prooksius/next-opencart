<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_pcolor".
 */
class Pcolor extends \yii\db\ActiveRecord
{
  public $name; 

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_pcolor';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['image', 'alias'], 'string'],
      [['sort_order'], 'integer'],
    ];
  }

  public static function getAliases()
  {
    return Yii::$app->cache->getOrSet('pcolor_aliases', function () {
      return self::find()
        ->select('alias')
        ->column();
    }, 3600);
  }

  public static function getAllPcolors()
  {
    $saved_items = Yii::$app->cache->getOrSet('pcolor_list', function () {
      return self::find()
        ->alias('pc')
        ->select([
          'pc.alias',
          'a.image',
          'pcd.name AS name',
        ])
        ->leftJoin('oc_pcolor_description pcd', 'pcd.pcolor_id = pc.id')
        ->where(['pcd.language_id' => \Yii::$app->language])
        ->all();
    }, 3600);

    $items = [];

    foreach ($saved_items as $item) {
      $items[] = [
        'alias' 	    => $item->alias,
        'name'  	    => $item->name,
        'icon' 		    => $item->image,
      ];
    }

    return $items;
  }

  public static function getSelectedColors($sel_colors)
  {
    $items = [];
    $colors = [];
    foreach ($sel_colors as $item) {
      $colors[] = Yii::$app->db->quoteValue($item);
    }
    $sel_colors = implode(", ", $colors);
    
    $selected = self::find()
      ->alias('pc')
      ->select([
        'pc.alias',
        'pc.image',
        'pcd.name'
      ])
      ->leftJoin('oc_pcolor_description pcd', 'pcd.pcolor_id = pc.id')
      ->where("pc.alias IN (" . $sel_colors . ") AND 
                          pcd.language_id = '" . \Yii::$app->language . "'")
      ->all();
    
    foreach ($selected as $item) {
      $items[$item->alias] = [
        'name' => $item->name,
        'icon' => $item->image,
      ];  
    }

    return $items;
  }
}