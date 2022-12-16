<?php

namespace app\models;

use common\models\Manufacturer as ModelsManufacturer;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "message".
 */
class Manufacturer extends ModelsManufacturer
{

  private static $_all_brands;

  public $name; 

  public static function getAllBrands()
  {
    if (!self::$_all_brands) {
      self::$_all_brands = ArrayHelper::map(
        self::find()
          ->alias('m')
          ->select([
            'm.id', 
            'md.name'
          ])
          ->leftJoin('oc_manufacturer_description md', '(md.manufacturer_id = m.id AND md.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('md.name ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_brands;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'image' => YII::t('app', 'Image'),
      'alias' => YII::t('app', 'Alias'),
      'sort_order' => YII::t('app', 'Sort Order'),
    ];
  }
}
