<?php

namespace app\models;

use Yii;
use backend\components\MyActiveRecord;
use common\models\Language;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_pcolor".
 */
class Pcolor extends MyActiveRecord
{
  protected $_desc_class = '\app\models\PcolorDesc';
  protected $_desc_id_name = 'pcolor_id';
  protected $_desc_fields = ['name'];

  private static $_all_colors;

  public $name; 

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_pcolor';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['languages'], 'safe'],
      [['image', 'alias'], 'string', 'max' => 255],
      [['sort_order'], 'integer'],
    ];
  }

  public static function getAllColors()
  {
    if (!self::$_all_colors) {
      self::$_all_colors = ArrayHelper::map(
        self::find()
          ->alias('pc')
          ->select([
            'pc.id', 
            'CONCAT(pcd.name, "###", pc.image) AS name'
          ])
          ->leftJoin('oc_pcolor_description pcd', '(pcd.pcolor_id = pc.id AND pcd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('pc.sort_order ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_colors;
  }

  public function getLanguagesList()
  {
    return Language::find()->all();
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'image' => YII::t('app', 'Image'),
      'alias' => YII::t('app', 'Alias'),
      'sort_order' => YII::t('app', 'Sort Order'),
    ];
  }
}