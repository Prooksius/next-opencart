<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_newsblog_category_description".
 */
class BlogCategoryDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_newsblog_category_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['category_id', 'language_id', 'name'], 'required'],
      [['category_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name', 'meta_title', 'meta_h1', 'meta_description', 'meta_keyword'], 'string', 'max' => 255],
      [['preview', 'description'], 'string'],
//      [['category_id', 'language_id'], 'unique', 'targetAttribute' => ['category_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'category_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'preview' => YII::t('blog', 'Preivew'),
      'description' => YII::t('app', 'Description'),
      'meta_title' => YII::t('app', 'Meta Title'),
      'meta_h1' => YII::t('app', 'Meta H1'),
      'meta_description' => YII::t('app', 'Meta Description'),
      'meta_keyword' => YII::t('app', 'Meta Keywords'),
    ];
  }
}