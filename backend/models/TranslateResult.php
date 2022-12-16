<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "translate_result".
 *
 * @property int $speciality_id
 * @property string $language_id
 * @property string $name
 */
class TranslateResult extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'translate_result';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['language_id', 'name'], 'required'],
            [['translate_word_id'], 'required', 'on' => 'update'],
            [['translate_word_id'], 'integer'],
            [['language_id'], 'string', 'max' => 10],
            [['name'], 'string'],
//            [['translate_word_id', 'language_id'], 'unique', 'targetAttribute' => ['translate_word_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'translate_word_id' => YII::t('app', 'ID'),
            'name' => YII::t('app', 'Name'),
        ];
    }
}