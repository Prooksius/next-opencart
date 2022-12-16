<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "menu_about".
 *
 * @property int $id
 * @property string $link
 * @property string $status
 * @property string $top_status
 * @property string $sort_order
 */
class MenuMain extends \yii\db\ActiveRecord
{
    public $name;
    private $_languages;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menu_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['link'], 'string', 'max' => 150],
            [['title'], 'string', 'max' => 200],
            [['sort_order', 'status', 'top_status', 'bottom_status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'sort_order' => 'Порядок сотрировки',
            'link' => 'Ссылка',
            'status' => 'Показывать на сайте',
            'top_status' => 'Показывать в шапке',
            'bottom_status' => 'Показывать в подвале',
        ];
    }
}
