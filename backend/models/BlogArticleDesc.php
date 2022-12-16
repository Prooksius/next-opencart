<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_newsblog_article_description".
 *
 * @property int $page_id
 * @property string $language_id
 * @property string $name
 * @property string $text
 */
class BlogArticleDesc extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_newsblog_article_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['article_id', 'language_id', 'name'], 'required'],
      [['article_id'], 'integer'],
      [['description', 'preview', 'tag'], 'string'],
      [['language_id'], 'string', 'max' => 10],
      [['name', 'meta_title', 'meta_h1', 'meta_description', 'meta_keyword'], 'string', 'max' => 255],
      // [['article_id', 'language_id'], 'unique', 'targetAttribute' => ['article_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'article_id' => YII::t('app', 'ID'),
      'language_id' => YII::t('app', 'Language ID'),
      'name' => YII::t('app', ' Name'),
      'tag' => YII::t('app', ' Tags'),
      'description' => YII::t('app', 'Description'),
      'preview' => YII::t('app', 'Preview'),
      'meta_title' => YII::t('app', 'META Title'),
      'meta_h1' => YII::t('app', 'META H1'),
      'meta_description' => YII::t('app', 'META Description'),
      'meta_keyword' => YII::t('app', 'META Keywords'),
    ];
  }
}