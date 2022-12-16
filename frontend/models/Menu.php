<?php

namespace frontend\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;

/**
 * This is the model class for table "menu".
 *
 * @property int $id
 * @property int $lft
 * @property int $rgt
 * @property int $depth
 * @property string $name
 * @property string $link
 * @property int $status
 */
class Menu extends \yii\db\ActiveRecord
{
    private $_languages;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menu';
    }

    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                // 'treeAttribute' => 'root',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new MenuQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'link', 'status'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['link'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Текст',
            'link' => 'Ссылка',
            'status' => 'Показывать на сайте',
        ];
    }
}
