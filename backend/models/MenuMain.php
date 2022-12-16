<?php

namespace app\models;

use Yii;
use backend\components\MyActiveRecord;

/**
 * This is the model class for table "menu_about".
 *
 * @property int $id
 * @property string $link
 * @property string $status
 * @property string $top_status
 * @property string $sort_order
 */
class MenuMain extends MyActiveRecord
{

    protected $_desc_class = '\app\models\MenuMainDesc';
    protected $_desc_id_name = 'menu_main_id';
    protected $_desc_fields = ['name'];

    public $name;

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
            'title' => 'Текст',
            'sort_order' => 'Порядок сотрировки',
            'link' => 'Ссылка',
            'status' => 'Показывать на сайте',
            'top_status' => 'Показывать в шапке',
            'bottom_status' => 'Показывать в подвале',
        ];
    }
}
