<?php

namespace app\models;

use common\models\Category as ModelsCategory;
use Yii;

/**
 * This is the model class for table "message".
 */
class Category extends ModelsCategory
{

  public $name;
  public $child_count;

  private static $_tree = null;
  private static $_count_level;

  private static function _getChilds($parent_id)
  {
    $childs_query = self::find()
      ->alias('c')
      ->select(['c.id', 'cd.name'])
      ->leftJoin('oc_category_description cd', '(cd.category_id = c.id AND cd.language_id = "' . \Yii::$app->language . '")')
      ->where(['parent_id' => $parent_id]);

    foreach ($childs_query->all() as $child) {
      self::$_tree[$child->id] = html_entity_decode(str_repeat('&nbsp;', self::$_count_level * 6) . $child->name);
      self::$_count_level++;
      self::_getChilds($child->id);
      self::$_count_level--;
    }
  }

  public static function getCategoriesTree()
  {
    if (!self::$_tree) {

      self::$_tree[0] = YII::t('category', 'Root Category');

      self::$_count_level = 0;
      self::_getChilds(0);
//      var_dump(self::$_tree);
    }
    return self::$_tree;

  }

  public function afterSave($insert, $changedAttributes)
  {
    parent::afterSave($insert, $changedAttributes);

    // MySQL Hierarchical Data Closure Table Pattern
    if ($insert) {
      $level = 0;

      $category_pathes = CategoryPath::find()
        ->where(['category_id' => $this->parent_id])
        ->orderBy(['level' => SORT_ASC])
        ->all();
      
      foreach ($category_pathes as $category_path) {
        
        Yii::$app->db
          ->createCommand()
          ->insert('oc_category_path', ['category_id' => (int)$this->id, 'path_id' => (int)$category_path->path_id, 'level' => (int)$level])
          ->execute();

        $level++;
      }

      Yii::$app->db
        ->createCommand()
        ->insert('oc_category_path', ['category_id' => (int)$this->id, 'path_id' => (int)$this->id, 'level' => (int)$level])
        ->execute();

    } else {
      $category_pathes = CategoryPath::find()
        ->where(['path_id' => $this->id])
        ->orderBy(['level' => SORT_ASC])
        ->all();
      
      if (count($category_pathes)) {
        foreach ($category_pathes as $category_path) {

          // Delete the path below the current one
          CategoryPath::deleteAll('category_id = ' . (int)$category_path->category_id . ' AND level < ' . (int)$category_path->level);
          
          $path = [];

          // Get the nodes new parents
          $parent_nodes = CategoryPath::find()
            ->where(['category_id' => $this->parent_id])
            ->orderBy(['level' => SORT_ASC])
            ->all();

          foreach ($parent_nodes as $parent_node) {
            $path[] = $parent_node->path_id;
          }

          // Get whats left of the nodes current path
          $left_nodes = CategoryPath::find()
            ->where(['category_id' => $category_path->category_id])
            ->orderBy(['level' => SORT_ASC])
            ->all();

          foreach ($left_nodes as $left_node) {
            $path[] = $left_node->path_id;
          }

          // Combine the paths with a new level
          $level = 0;

          foreach ($path as $path_id) {
            Yii::$app->db
              ->createCommand()
              ->upsert('oc_category_path', ['category_id' => (int)$category_path->category_id, 'path_id' => (int)$path_id, 'level' => (int)$level])
              ->execute();

            $level++;
          }

        }
      } else {

        // Delete the path below the current one
        CategoryPath::deleteAll('category_id = ' . (int)$this->id);

        // Fix for records with no paths
        $level = 0;

        $category_pathes = CategoryPath::find()
          ->where(['category_id' => $this->parent_id])
          ->orderBy(['level' => SORT_ASC])
          ->all();
      
        foreach ($category_pathes as $category_path) {
          Yii::$app->db
            ->createCommand()
            ->insert('oc_category_path', ['category_id' => (int)$this->id, 'path_id' => (int)$category_path->path_id, 'level' => (int)$level])
            ->execute();

          $level++;
        }

        Yii::$app->db
          ->createCommand()
          ->upsert('oc_category_path', ['category_id' => (int)$this->id, 'path_id' => (int)$this->id, 'level' => (int)$level])
          ->execute();
      }
    }

  }

  public static function repairCategories($parent_id = 0) 
  {
    $childs = self::find()
      ->where(['parent_id' => $parent_id])
      ->all();
    
    foreach ($childs as $child) {

      // Delete the path below the current one
      CategoryPath::deleteAll('category_id = ' . (int)$child->id);

      // Fix for records with no paths
			$level = 0;

      $category_pathes = CategoryPath::find()
        ->where(['category_id' => (int)$parent_id])
        ->orderBy(['level' => SORT_ASC])
        ->all();
    
      foreach ($category_pathes as $category_path) {
        Yii::$app->db
          ->createCommand()
          ->insert('oc_category_path', ['category_id' => (int)$child->id, 'path_id' => (int)$category_path->path_id, 'level' => (int)$level])
          ->execute();

        $level++;
      }

      Yii::$app->db
        ->createCommand()
        ->upsert('oc_category_path', ['category_id' => (int)$child->id, 'path_id' => (int)$child->id, 'level' => (int)$level])
        ->execute();

      // рекурсивно вызываем для всех детей этой категории
      self::repairCategories((int)$child->id);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'parent_id' => YII::t('category', 'Parent Category'),
      'child_count' => YII::t('category', 'Child Count'),
      'name' => YII::t('app', 'Name'),
      'image' => YII::t('app', 'Image'),
      'alias' => YII::t('app', 'Alias'),
      'top' => YII::t('category', 'Top'),
      'status' => YII::t('app', 'Status'),
      'sort_order' => YII::t('app', 'Sort Order'),
    ];
  }
}
