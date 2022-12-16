<?php

namespace app\models;

use backend\components\MyActiveRecord;
use common\models\Language;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_newsblog_article".
 */
class BlogArticle extends MyActiveRecord
{

  protected $_desc_class = '\app\models\BlogArticleDesc';
  protected $_desc_id_name = 'article_id';
  protected $_desc_fields = ['name', 'preview', 'description', 'tag', 'meta_title', 'meta_h1', 'meta_description', 'meta_keyword'];

  private $_categoryIds;
  public $main_category_id;

  private $_relatedIds;

  public $name;
  public $image_count;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_newsblog_article';
  }

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
      [['alias', 'image'], 'string', 'max' => 255],
      [['date_available', 'sort_order', 'viewed', 'status', 'featured'], 'integer'],
      [['languages', 'name', 'categoryIds', 'relatedIds', 'dateavailable', 'main_category_id'], 'safe'],
    ];
  }

  public function getDateavailable()
  {
    if ($this->date_available) {
      return date('d.m.Y H:i', $this->date_available);
    } else {
      return '';
    }
  }

  public function setDateavailable($value)
  {
    if ($value) {
      $date_field = date_create_from_format('d.m.Y H:i', $value);
      $this->date_available = date_timestamp_get($date_field);
    } else {
      $this->date_available = 0;
    }
  }

  public function getLanguagesList()
  {
    return Language::find()->all();
  }
  public function getCategoriesTree()
  {
    return BlogCategory::getCategoriesTree();
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCategories()
  {
    /*
    return $this
      ->hasMany(BlogCategory::className(), ['id' => 'category_id'])
      ->viaTable('oc_newsblog_article_to_category', ['article_id' => 'id']);
    */

    return BlogCategory::find()
      ->alias('bc')
      ->select(['bc.*'])
      ->leftJoin('oc_newsblog_article_to_category ba2bc', 'ba2bc.category_id = bc.id')
      ->where(['ba2bc.article_id' => $this->id])
      ->all();
  }

  public function fetchMainCategoryId()
  {
    $main_category = BlogArticleToCategory::find()
      ->select('category_id')
      ->where(['article_id' => $this->id, 'main_category' => 1])
      ->one();

    if ($main_category) {
      return $main_category->category_id;
    }

    return 0;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getRelateds()
  {
    return BlogArticleRelated::find()
      ->where(['article_id' => $this->id])
      ->all();
  }

  /**
   * @inheritdoc
   */
  public function afterSave($insert, $changedAttributes)
  {
    parent::afterSave($insert, $changedAttributes);

    if (!$insert) {

      if (empty($this->_categoryIds)) {
        $this->getCategoryIds();
      }
      if (empty($this->main_category_id)) {
        $this->main_category_id = $this->fetchMainCategoryId();
      }

      if (empty($this->_relatedIds)) {
        $this->getRelatedIds();
      }

      static::getDb()
        ->createCommand()
        ->delete('oc_newsblog_article_to_category', ['article_id' => $this->id])
        ->execute();

      static::getDb()
        ->createCommand()
        ->delete('oc_newsblog_article_related', ['article_id' => $this->id])
        ->execute();
    }

    if (!empty($this->categoryIds)) {

      static::getDb()
        ->createCommand()
        ->batchInsert(
          'oc_newsblog_article_to_category',
          ['article_id', 'category_id', 'main_category'],
          array_map(function ($categoryId) { return [$this->id, $categoryId, (int)($categoryId == $this->main_category_id)]; }, $this->categoryIds)
        )
        ->execute();

      static::getDb()
        ->createCommand()
        ->batchInsert(
          'oc_newsblog_article_related',
          ['article_id', 'related_id'],
          array_map(function ($relatedId) { return [$this->id, $relatedId]; }, $this->relatedIds)
        )
        ->execute();

      //var_dump($this->main_category_id);  
    }    
  }

  /**
   * @return array
   */
  public function getCategoryIds()
  {
    if ($this->_categoryIds === null) {
      $this->_categoryIds = ArrayHelper::getColumn($this->categories, 'id');
    }

    return $this->_categoryIds;
  }

  /**
   * @param $value array
   */
  public function setCategoryIds($value) {
    $this->_categoryIds = $value;
  }

  /**
   * @return array
   */
  public function getRelatedIds()
  {
    if ($this->_relatedIds === null) {
      $this->_relatedIds = ArrayHelper::getColumn($this->relateds, 'related_id');
    }

    return $this->_relatedIds;
  }

  /**
   * @param $value array
   */
  public function setRelatedIds($value) {
    $this->_relatedIds = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'category_id' => YII::t('blog', 'Blog Category'),
      'image' => YII::t('app', 'Image'),
      'alias' => YII::t('app', 'Alias'),
      'featured' => YII::t('blog', 'Featured Article'),
      'date_available' => YII::t('blog', 'Publication Date'),
      'dateavailable' => YII::t('blog', 'Publication Date'),
      'name' => YII::t('app', 'Name'),
      'preview' => YII::t('blog', 'Preview'),
      'description' => YII::t('app', 'Content'),
      'tag' => YII::t('app', 'Tags'),
      'meta_title' => YII::t('app', 'META Title'),
      'meta_h1' => YII::t('app', 'META H1'),
      'meta_description' => YII::t('app', 'META Description'),
      'meta_keyword' => YII::t('app', 'META Keywords'),
      'viewed' => YII::t('app', 'Views'),
      'sort_order' => YII::t('app', 'Sort order'),
      'relatedIds' => YII::t('product', 'Related products'),
      'status' => YII::t('app', 'Status'),
    ];
  }
}
