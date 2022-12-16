<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "language".
 */
class Manufacturer extends MyActiveRecord
{

  protected $_desc_class = '\common\models\ManufacturerDesc';
  protected $_desc_id_name = 'manufacturer_id';
  protected $_desc_fields = ['name', 'description', 'meta_title', 'meta_h1', 'meta_description', 'meta_keyword'];

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
      [['alias'], 'required'],
      [['alias'], 'unique'],
      [['sort_order'], 'integer'],
      [['alias'], 'string', 'max' => 100],
      [['image'], 'string', 'max' => 255],
      [['languages'], 'safe'],
    ];
  }
}
