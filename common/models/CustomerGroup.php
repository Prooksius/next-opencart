<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "oc_customer_group".
 *
 * @property int $id
 * @property string $name
 * @property string $locale
 */
class CustomerGroup extends MyActiveRecord
{

  protected $_desc_class = '\common\models\CustomerGroupDesc';
  protected $_desc_id_name = 'customer_group_id';
  protected $_desc_fields = ['name', 'description'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_customer_group';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['sort_order', 'approval'], 'integer'],
      [['languages'], 'safe'],
    ];
  }
}
