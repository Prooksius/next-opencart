<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_product_description".
 */
class ProductDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'language_id', 'name', 'short_name'], 'required'],
      [['product_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 255],
      [['short_name'], 'string', 'max' => 100],
      [['meta_title', 'meta_h1', 'meta_description', 'meta_keyword'], 'string', 'max' => 255],
      [['description', 'tag'], 'string'],
//      [['product_id', 'language_id'], 'unique', 'targetAttribute' => ['product_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'product_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'short_name' => YII::t('product', 'Catalog Name'),
      'description' => YII::t('app', 'Description'),
      'tag' => YII::t('app', 'Tag'),
      'meta_title' => YII::t('app', 'Meta Title'),
      'meta_h1' => YII::t('app', 'Meta H1'),
      'meta_description' => YII::t('app', 'Meta Description'),
      'meta_keyword' => YII::t('app', 'Meta Keywords'),
    ];
  }
}