<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "country_desc".
 *
 * @property int $page_id
 * @property string $language_id
 * @property string $name
 * @property string $text
 */
class CountryDesc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_country_desc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'language_id', 'name'], 'required'],
            [['country_id'], 'integer'],
            [['language_id'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 255],
            [['country_id', 'language_id'], 'unique', 'targetAttribute' => ['country_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'country_id' => YII::t('app', 'ID'),
            'language_id' => YII::t('app', 'Language ID'),
            'name' => YII::t('app', ' Name'),
            'title' => YII::t('app', ' Title'),
        ];
    }
}