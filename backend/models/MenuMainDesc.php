<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "menu_main_desc".
 *
 * @property int $menu_main_id
 * @property string $language_id
 * @property string $name
 */
class MenuMainDesc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menu_main_desc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menu_main_id', 'language_id', 'name'], 'required'],
            [['menu_main_id'], 'integer'],
            [['language_id'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 150],
//            [['menu_main_id', 'language_id'], 'unique', 'targetAttribute' => ['menu_main_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'menu_main_id' => 'ID',
            'name' => 'name',
        ];
    }
}