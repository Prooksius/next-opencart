<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "language".
 *
 * @property int $id
 * @property string $name
 * @property string $locale
 */
class AttributeGroup extends MyActiveRecord
{

  protected $_desc_class = '\common\models\AttributeGroupDesc';
  protected $_desc_id_name = 'attribute_group_id';
  protected $_desc_fields = ['name'];

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
      [['languages'], 'safe'],
    ];
  }
}
