<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_layout".
 */
class Layout extends \yii\db\ActiveRecord
{

  private $_modules;

  public $instances;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_layout';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id'], 'integer'],
      [['name', 'code'], 'string'],
      ['modules', 'safe'],
    ];
  }

  public function getModules()
  {
    if ($this->_modules === null) {
      $this->_modules = [
        'page-top' => [],
        'column-left' => [],
        'column-right' => [],
        'content-top' => [],
        'content-bottom' => [],
        'page-bottom' => [],
      ];
      $records = LayoutModule::find()
        ->where(['layout_id' => $this->id])
        ->orderBy(['sort_order' => SORT_ASC])
        ->all();
      
      foreach ($records as $record) {
        $this->_modules[$record->position][] = [
          'module_id' => $record->module_id,
          'sort_order' => $record->sort_order,
        ];
      }
    }

    return $this->_modules;
  }

  public function setModules($value)
  {
    $this->_modules = $value;
  }

  /**
   * @inheritdoc
   */
  public function beforeSave($insert)
  {

    if (!$insert) {

      if (empty($this->_modules)) {
        $this->getModules();
      }

      static::getDb()
        ->createCommand()
        ->delete('oc_layout_module', ['layout_id' => $this->id])
        ->execute();
    }

    if (!empty($this->_modules)) {

      $rows = [];
      foreach ($this->_modules as $position => $modules) {
        foreach ($modules as $module) {
          $rows[] = [$this->id, $position, $module['module_id'], $module['sort_order']];
        }
      }

      static::getDb()
        ->createCommand()
        ->batchInsert(
          'oc_layout_module',
          ['layout_id', 'position', 'module_id', 'sort_order'],
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
      'name' => YII::t('app', 'Name'),
      'code' => YII::t('app', 'Code'),
      'instances' => YII::t('layout', 'Attached modules'),
    ];
  }
}
