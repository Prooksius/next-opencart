<?php

namespace app\models;

use backend\components\MyActiveRecord;
use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property int $id
 * @property string $link
 */
class Pages extends MyActiveRecord
{
    protected $_desc_class = '\app\models\PagesDesc';
    protected $_desc_id_name = 'page_id';
    protected $_desc_fields = ['name', 'title', 'subtitle', 'text', 'page_title', 'page_desc', 'page_kwords'];

    public $name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image', 'sef'], 'string', 'max' => 255],
            ['sef', 'unique'],
            [['languages', 'name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => YII::t('app', 'ID'),
            'image' => YII::t('app', 'Page Image'),
            'name' => YII::t('app', 'Name'),
            'sef' => YII::t('app', 'SEO address'),
            'text' => YII::t('app', 'Content'),
            'page_title' => YII::t('app', 'META Title'),
            'page_desc' => YII::t('app', 'META Description'),
            'page_kwords' => YII::t('app', 'META Keywords'),
        ];
    }
}
