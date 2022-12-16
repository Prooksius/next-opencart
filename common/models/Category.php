<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "oc_category".
 */
class Category extends MyActiveRecord
{

  protected $_desc_class = '\common\models\CategoryDesc';
  protected $_desc_id_name = 'category_id';
  protected $_desc_fields = ['name', 'description', 'meta_title', 'meta_h1', 'meta_description', 'meta_keyword'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_category';
  }

  public function beforeSave($insert)
  {
    if (!$insert) {
      if ($this->id == $this->parent_id) {
        $this->addError('parent_id', YII::t('category', 'Category cannot have parent as itself'));
        return false;
      }
    }

    return parent::beforeSave($insert);
  }

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      TimestampBehavior::className(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['alias'], 'required'],
      [['alias'], 'unique'],
      [['id', 'sort_order', 'parent_id', 'top', 'status'], 'integer'],
      [['alias'], 'string', 'max' => 100],
      [['image'], 'string', 'max' => 255],
      [['languages'], 'safe'],
    ];
  }
}
