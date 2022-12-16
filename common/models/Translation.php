<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "translate_word".
 */
class Translation extends \yii\db\ActiveRecord
{
    public $translation;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'translate_word';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phrase'], 'string'],
        ];
    }

    /**
     * @return string
     */
    public static function getTranslation($phrase, $lang = '')
    {
        $language = $lang ? $lang : (Yii::$app->language ? Yii::$app->language : 'en-US');

        $translation = self::find()
            ->alias('tr')
            ->select(['trr.name as translation'])
            ->leftJoin('translate_result trr', 'trr.translate_word_id = tr.id AND trr.language_id = "' . $language . '"')
            ->where(['tr.phrase' => $phrase])
            ->one();

        if ($translation instanceof Translation) {
            return $translation->translation;
        } else {
            return $phrase;
        }
    }
}
