<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "oc_newsblog_category".
 */
class BlogCategory extends \yii\db\ActiveRecord
{

  public $name;
  public $preview;
  public $child_count;
  public $article_count;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_newsblog_category';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['sort_order', 'parent_id', 'status'], 'integer'],
      [['alias', 'image'], 'string'],
    ];
  }

  private static function _getCategory($param)
  {
    $query = self::find()
      ->alias('bc')
      ->select([
        'bc.*',
        'bcd.*'
      ])
      ->leftJoin('oc_newsblog_category_description bcd', '(bcd.category_id = bc.id AND bcd.language_id = "' . \Yii::$app->language . '")');

      if (!empty($param['id'])) {
        $query->where(['bc.id' => $param['id']]);
        return $query->asArray()->one();
      } elseif (!empty($param['alias'])){
        $query->where(['bc.alias' => $param['alias']]);
        return $query->asArray()->one();
      }
      return false;
  }

  public static function getCategories(int $parent_id)
  {
    $categories = [];
    
    $childs_query = self::find()
      ->alias('bc')
      ->select([
        'bc.*', 
        'bcd.name AS name',
        'bcd.preview AS preview'
      ])
      ->leftJoin('oc_newsblog_category_description bcd', 'bcd.category_id = bc.id AND bcd.language_id = "' . \Yii::$app->language . '"')
      ->where(['bc.parent_id' => $parent_id, 'bc.status' => 1]);

    foreach ($childs_query->all() as $child) {
      $categories[] = [
        'id' => $child->id,
        'name' => $child->name,
        'alias' => $child->alias,
        'image' => $child->image,
        'preview' => $child->preview,
      ];
    }

    return $categories;
  }

  public static function getCategory($id)
  {
    return self::_getCategory(['id' => $id]);
  }

  public static function getCategoryBySlug($slug)
  {
    return self::_getCategory(['alias' => $slug]);
  }

  public static function getBreadcrumbs(array $slugs, string $lastSlug)
  {
    $breadcrumbs = [
      [
        'title' => Translation::getTranslation('Blog'),
        'href' => !empty($slugs) || $lastSlug ? '/blog' : '',
      ],
    ];
    $href = ['blog'];
    foreach ($slugs as $item) {
      $bread_category = self::getCategoryBySlug($item);
      $href[] = $item;
      $breadcrumbs[] = [
        'title' => $bread_category['name'],
        'href' => '/'. implode('/', $href),
      ];
    }

    if ($lastSlug) {
      $current_category = self::getCategoryBySlug($lastSlug);
      $breadcrumbs[] = [
        'title' => $current_category['name'],
        'href' => '',
      ];
    }

    return $breadcrumbs;
  }
}
