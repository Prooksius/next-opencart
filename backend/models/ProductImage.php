<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_product_image".
 *
 * @property int $id
 * @property string $name
 * @property string $text
 */
class ProductImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_product_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image'], 'required'],
            [['product_id', 'sort_order'], 'integer'],
            [['image'], 'string', 'max' => 255],
        ];
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
            'sort_order' => YII::t('app', 'Sort Order'),
        ];
    }
}
