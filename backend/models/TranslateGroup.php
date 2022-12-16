<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "translate_result".
 *
 * @property int $speciality_id
 * @property string $language_id
 * @property string $name
 */
class TranslateGroup extends \yii\db\ActiveRecord
{
  private static $_all_groups;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'translate_group';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['code', 'name'], 'required'],
      [['code'], 'string', 'max' => 50],
      [['name'], 'string', 'max' => 100],
    ];
  }

  public static function getAllGroups()
  {
    if (!self::$_all_groups) {
      self::$_all_groups = ArrayHelper::map(
        self::find()
          ->select([
            'id', 
            'name'
          ])
          ->orderBy('name ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_groups;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'code' => YII::t('app', 'Code'),
      'name' => YII::t('app', 'Name'),
    ];
  }
}