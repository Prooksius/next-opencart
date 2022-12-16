<?php

namespace frontend\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "oc_layout_module".
 */
class LayoutModule extends \yii\db\ActiveRecord
{

  public $moduleSettings;
  public $pageCode;
  public $moduleClass;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_layout_module';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['layout_id', 'module_id', 'sort_order'], 'integer'],
      [['position'], 'string'],
    ];
  }

  public static function getPageModules($page_code, $params)
  {
    $modules = [];

    $records = self::find()
      ->alias('lm')
      ->select([
        'lm.*',
        'mg.code AS moduleClass',
        'm.settings AS moduleSettings',
      ])
      ->leftJoin('oc_layout l', 'l.id = lm.layout_id')
      ->leftJoin('oc_module m', 'm.id = lm.module_id')
      ->leftJoin('oc_module_group mg', 'mg.id = m.module_group_id')
      ->where(['m.status' => 1, 'l.code' => $page_code])
      ->orderBy(['lm.sort_order' => SORT_ASC])
      ->all();
    
    foreach ($records as $record) {
      if (!isset($modules[$record->position])) {
        $modules[$record->position] = [];
      }

      $moduleClass = 'frontend\extensions\modules\\' . $record->moduleClass;

      $modules[$record->position][] = [
        'moduleClass' => $record->moduleClass,
        'content' => $moduleClass::getContent(Json::decode($record->moduleSettings, true), $params),
      ];
    }

    return $modules;
  }

  public static function getAllModules()
  {
    $modules = [];

    $records = self::find()
      ->alias('lm')
      ->select([
        'l.code AS pageCode',
        'lm.*',
        'mg.code AS moduleClass',
        'm.settings AS moduleSettings',
      ])
      ->leftJoin('oc_layout l', 'l.id = lm.layout_id')
      ->leftJoin('oc_module m', 'm.id = lm.module_id')
      ->leftJoin('oc_module_group mg', 'mg.id = m.module_group_id')
      ->where(['m.status' => 1])
      ->orderBy(['lm.sort_order' => SORT_ASC])
      ->all();
    
    foreach ($records as $record) {
      if (!isset($modules[$record->pageCode])) {
        $modules[$record->pageCode] = [];
      }
      if (!isset($modules[$record->pageCode][$record->position])) {
        $modules[$record->pageCode][$record->position] = [];
      }

      $modules[$record->pageCode][$record->position][] = [
        'moduleClass' => $record->moduleClass,
        'settings' => Json::decode($record->moduleSettings, true),
      ];
    }

    return $modules;
  }
}
