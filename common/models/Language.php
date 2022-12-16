<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "language".
 *
 * @property int $id
 * @property string $name
 * @property string $locale
 */
class Language extends \yii\db\ActiveRecord
{

  private static $_all_langs;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_language';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name', 'locale'], 'required'],
      [['name'], 'string', 'max' => 64],
      [['locale'], 'string', 'max' => 10],
    ];
  }

  public function getCode()
  {
    return strtolower(explode('-', $this->locale)[1]);
  }

  public static function getList()
  {
    if (!self::$_all_langs) {
      self::$_all_langs = ArrayHelper::map(
        self::find()
          ->select([
            'locale', 
            'name'
          ])
          ->orderBy('name ASC')
          ->all(),
        'locale', 'name');
    }
    return self::$_all_langs;
  }

  public function isDefault()
  {
    return Yii::$app->shopConfig->getParam('language') == $this->locale;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'locale' => YII::t('app', 'Locale'),
    ];
}
}
