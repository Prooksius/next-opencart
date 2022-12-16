<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "reviews".
 *
 * @property int $id
 * @property string $name
 * @property string $position
 * @property string $photo
 * @property string $description
 * @property int $status
 */
class Reviews extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'city', 'photo', 'status'], 'required'],
            [['description'], 'string'],
            [['status', 'period', 'info_type'], 'integer'],
            [['deposit'], 'number'],
            [['name'], 'string', 'max' => 200],
            [['income'], 'string', 'max' => 100],
            [['video', 'picture'], 'string', 'max' => 255],
            [['city'], 'string', 'max' => 300],
            [['photo'], 'string', 'max' => 500],
        ];
    }
}
