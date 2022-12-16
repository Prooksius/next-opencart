<?php

namespace app\models;

use common\models\Language;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_banner".
 */
class Banner extends \yii\db\ActiveRecord
{
  
  private static $_all_banners;
  private $_images;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_banner';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name', 'status'], 'required'],
      [['status'], 'integer'],
      [['name'], 'string', 'max' => 64],
      [['images'], 'safe'],
    ];
  }

  public function getLanguagesList()
  {
    return Language::find()->all();
  }

  public static function getAllbanners()
  {
    if (!self::$_all_banners) {
      self::$_all_banners = ArrayHelper::map(
        self::find()
          ->alias('b')
          ->select([
            'b.id', 
            'b.name'
          ])
          ->orderBy('b.name ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_banners;
  }

  /**
   * @return array
   */
  public function getImages()
  {
    if ($this->_images === null) {
      $this->_images = [];

      foreach ($this->languagesList as $language) {
        $this->_images[$language->locale] = [];
      }

      $images = BannerImage::find()
        ->where(['banner_id' => $this->id])
        ->orderBy(['sort_order' => SORT_ASC])
        ->all();

      foreach ($images as $image) {
        $this->_images[$image->language_id][] = [
          'title' => $image->title,
          'text1' => $image->text1,
          'text2' => $image->text2,
          'text3' => $image->text3,
          'image' => $image->image,
          'link' => $image->link,
          'sort_order' => $image->sort_order,
        ];
      }
    }

    return $this->_images;
  }

  /**
   * @param $value array
   */
  public function setImages($value) {
    $this->_images = $value;
  }


  /**
   * @inheritdoc
   */
  public function beforeSave($insert)
  {

    if (!$insert) {

      if (empty($this->_images)) {
        $this->getImages();
      }

      static::getDb()
        ->createCommand()
        ->delete('oc_banner_image', ['banner_id' => $this->id])
        ->execute();
    }

    if (!empty($this->_images)) {

      $rows = [];
      foreach ($this->_images as $language_id => $lang_images) {
        foreach ($lang_images as $image) {
          $rows[] = [$this->id, $language_id, $image['title'], $image['text1'], $image['text2'], $image['text3'], $image['link'], $image['image'], $image['sort_order']];
        }
      }

      static::getDb()
        ->createCommand()
        ->batchInsert(
          'oc_banner_image',
          ['banner_id', 'language_id', 'title', 'text1', 'text2', 'text3', 'link', 'image', 'sort_order'],
          $rows
        )
        ->execute();
    }

    return parent::beforeSave($insert);
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'title' => YII::t('app', 'Title'),
      'text1' => YII::t('app', 'Text1'),
      'text2' => YII::t('app', 'Text2'),
      'text3' => YII::t('app', 'Text3'),
      'sort_order' => YII::t('app', 'Sort_order'),
      'status' => YII::t('app', 'Active?'),
    ];
  }
}
