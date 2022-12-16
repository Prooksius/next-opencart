<?php

namespace app\models;

/**
 * This is the model class for table "reviews".
 */
class ProductToCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_product_to_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'product_id', 'main_category'], 'integer'],
        ];
    }
}
