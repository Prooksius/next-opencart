<?php

namespace app\models;

use common\models\Language;
use Yii;

/**
 * This is the model class for table "oc_product_image".
 *
 * @property int $id
 * @property string $name
 * @property string $text
 */
class ProductAttribute extends \yii\db\ActiveRecord
{
    public $_textsarr;
    public $attributeName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_product_attribute';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_id'], 'unique', 'targetAttribute' => ['attribute_id', 'product_id', 'language_id'], 
              'message' => YII::t('product', 'This attribute is already added to the product')],
            [['alias'], 'required'],
            [['product_id', 'attribute_id'], 'integer'],
            [['alias'], 'string', 'max' => 100],
            [['language_id'], 'string', 'max' => 10],
            [['text'], 'string'],
            [['textsarr'], 'safe'],
            ['textsarr', function ($attribute, $params) {
              foreach ($this->$attribute as $lang => $item) {
                if (empty($item)) {
                  $this->addError($attribute, [$lang => YII::t('app', 'Field is required')]);
                  break;
                }
              }
            }],
        ];
    }

    public function validateTextsarr() 
    {

    }

    public function getTextsarr()
    {
      if (empty($this->_textsarr)) {
        $this->_textsarr = [];
        $langAttrs = self::find()->where(['product_id' => $this->product_id, 'attribute_id' => $this->attribute_id])->all();
        foreach ($langAttrs as $langAttr) {
          $this->_textsarr[$langAttr->language_id] = $langAttr->text;
        }
      }
      return $this->_textsarr;
    }

    public function setTextsarr($value)
    {
      $this->_textsarr = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => YII::t('app', 'ID'),
            'image' => YII::t('app', 'Image'),
            'product_id' => YII::t('product', 'Product'),
            'attribute_id' => YII::t('attribute', 'Attribute'),
            'attributeName' => YII::t('attribute', 'Attribute'),
            'alias' => YII::t('app', 'Alias'),
            'text' => YII::t('app', 'Text'),
        ];
    }
}
