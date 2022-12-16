<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "faq_desc".
 *
 * @property int $speciality_id
 * @property string $language_id
 * @property string $name
 */
class FaqDesc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_faq_desc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['faq_id', 'language_id', 'name', 'description'], 'required'],
            [['faq_id'], 'integer'],
            [['language_id'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
//            [['faq_id', 'language_id'], 'unique', 'targetAttribute' => ['faq_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'faq_id' => YII::t('app', 'ID'),
            'name' => YII::t('app', 'Name'),
            'description' => YII::t('app', 'Description'),
        ];
    }
}