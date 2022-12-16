<?php

namespace app\models;

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
            [['income'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 200],
            [['video', 'picture'], 'string', 'max' => 255],
            [['city'], 'string', 'max' => 300],
            [['photo'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'city' => 'Город',
            'photo' => 'Фото',
            'description' => 'Отзыв',
            'picture' => 'Картинка',
            'video' => 'Видео',
            'deposit' => 'Депозит, $',
            'period' => 'Период дней',
            'income' => 'Прибыль, $',
            'info_type' => 'Тип информации',
            'status' => 'Показывать на сайте',
        ];
    }
}
