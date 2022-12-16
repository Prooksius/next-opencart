<?php

namespace frontend\models;

/**
 * This is the model class for table "oc_newsblog_article_to_category".
 */
class BlogArticleToCategory extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_newsblog_article_to_category';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['category_id', 'article_id', 'main_category'], 'integer'],
    ];
  }
}
