<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use backend\components\MyActiveRecord;
use common\models\Language;

/**
 * This is the model class for table "message".
 */
class Product extends MyActiveRecord
{

  protected $_desc_class = '\app\models\ProductDesc';
  protected $_desc_id_name = 'product_id';
  protected $_desc_fields = ['name', 'short_name', 'description', 'tag', 'meta_title', 'meta_h1', 'meta_description', 'meta_keyword'];

  private $_categoryIds;

  public $name; 
  public $image_count;
  public $attribute_count;
  public $filter_count;
  public $special_count;
  public $discount_count;
  public $option_count;


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['alias', 'model', 'sku'], 'required'],
      [['alias'], 'unique'],
      [['sort_order', 'quantity', 'stock_status_id', 'manufacturer_id', 'shipping', 'points', 
        'date_available', 'weight_class_id', 'length_class_id', 'subtract', 'minimum', 'status', 'viewed'], 'integer'],
      [['sku', 'mpn'], 'string', 'max' => 64],
      [['upc'], 'string', 'max' => 12],
      [['ean'], 'string', 'max' => 14],
      [['jan'], 'string', 'max' => 13],
      [['isbn'], 'string', 'max' => 17],
      [['location'], 'string', 'max' => 128],
      [['alias', 'image'], 'string', 'max' => 255],
      [['price', 'weight', 'length', 'width', 'height'], 'number'],
      [['languages', 'categoryIds', 'dateavailable'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      TimestampBehavior::className(),
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

  public function getStockStatusesList()
  {
    return StockStatus::getAllStatuses();
  }
  public function getLengthClassesList()
  {
    return LengthClass::getAllClasses();
  }
  public function getWeightClassesList()
  {
    return WeightClass::getAllClasses();
  }
  public function getManufacturersList()
  {
    return Manufacturer::getAllBrands();
  }
  public function getLanguagesList()
  {
    return Language::find()->all();
  }
  public function getCategoriesTree()
  {
    return Category::getCategoriesTree();
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCategories()
  {
    return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable('oc_product_to_category', ['product_id' => 'id']);
  }

  /**
   * @inheritdoc
   */
  public function beforeSave($insert)
  {

    if (!$insert) {

      if (empty($this->_categoryIds)) {
        $this->getCategoryIds();
      }

      static::getDb()
        ->createCommand()
        ->delete('oc_product_to_category', ['product_id' => $this->id])
        ->execute();
    }

    if (!empty($this->categoryIds)) {

      static::getDb()
        ->createCommand()
        ->batchInsert(
          'oc_product_to_category',
          ['product_id', 'category_id'],
          array_map(function ($categoryId) { return [$this->id, $categoryId]; }, $this->categoryIds)
        )
        ->execute();
      //var_dump($this->main_category_id);  
    }
    
    return parent::beforeSave($insert);
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
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'image' => YII::t('product', 'Image'),
      'alias' => YII::t('app', 'Alias'),
      'model' => YII::t('product', 'Model'),
      'sku' => YII::t('product', 'SKU'),
      'upc' => YII::t('product', 'UPC'),
      'ean' => YII::t('product', 'EAN'),
      'jan' => YII::t('product', 'JAN'),
      'isbn' => YII::t('product', 'ISBN'),
      'mpn' => YII::t('product', 'MPN'),
      'location' => YII::t('product', 'Location'),
      'quantity' => YII::t('product', 'Quantity'),
      'stock_status_id' => YII::t('product', 'Stock status'),
      'category_id' => YII::t('category', 'Category'),
      'manufacturer_id' => YII::t('manufacturer', 'Manufacturer'),
      'main_category_id' => YII::t('product', 'Main Category'),
      'manufacturer' => YII::t('product', 'Manufacturer'),
      'shipping' => YII::t('app', 'Shipping'),
      'price' => YII::t('app', 'Price'),
      'points' => YII::t('product', 'Points'),
      'date_available' => YII::t('product', 'Date Available'),
      'dateavailable' => YII::t('product', 'Date Available'),
      'weight' => YII::t('product', 'Weight'),
      'weight_class_id' => YII::t('localisation', 'Weight class'),
      'length' => YII::t('product', 'Length'),
      'width' => YII::t('product', 'Width'),
      'height' => YII::t('product', 'Height'),
      'length_class_id' => YII::t('localisation', 'Length class'),
      'subtract' => YII::t('product', 'Subtract'),
      'minimum' => YII::t('app', 'Minimum'),
      'viewed' => YII::t('product', 'Viewed'),
      'status' => YII::t('app', 'Status'),
      'sort_order' => YII::t('app', 'Sort Order'),
      'short_name' => YII::t('product', 'Catalog Name'),
      'description' => YII::t('app', 'Description'),
      'tag' => YII::t('app', 'Tags'),
      'meta_title' => YII::t('app', 'Meta Title'),
      'meta_h1' => YII::t('app', 'Meta H1'),
      'meta_description' => YII::t('app', 'Meta Description'),
      'meta_keyword' => YII::t('app', 'Meta Keywords'),
    ];
  }
}
