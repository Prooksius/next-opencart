<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_category_path".
 */
class CategoryPath extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_category_path';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'path_id', 'level'], 'required'],
            [['category_id', 'path_id', 'level'], 'integer'],
            [['category_id', 'path_id'], 'unique'],
        ];
    }
}