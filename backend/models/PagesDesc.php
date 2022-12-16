<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pages_desc".
 *
 * @property int $page_id
 * @property string $language_id
 * @property string $name
 * @property string $text
 */
class PagesDesc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pages_desc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['page_id', 'language_id', 'name'], 'required'],
            [['page_id'], 'integer'],
            [['subtitle', 'text'], 'string'],
            [['language_id'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 150],
            [['title', 'page_title', 'page_kwords'], 'string', 'max' => 255],
            [['page_desc'], 'string', 'max' => 500],
            [['page_id', 'language_id'], 'unique', 'targetAttribute' => ['page_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'page_id' => YII::t('app', 'ID'),
            'language_id' => YII::t('app', 'Language ID'),
            'name' => YII::t('app', ' Name'),
            'title' => YII::t('app', ' Title'),
            'subtitle' => YII::t('app', ' Subtitle'),
            'text' => YII::t('app', 'Text'),
            'page_title' => YII::t('app', 'META Title'),
            'page_desc' => YII::t('app', 'META Description'),
            'page_kwords' => YII::t('app', 'META Keywords'),
        ];
    }
}