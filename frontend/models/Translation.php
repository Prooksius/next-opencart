<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "translate_word".
 *
 * @property int $id
 * @property string $link
 */
class Translation extends \yii\db\ActiveRecord
{
    private static $_all_phrases;
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
     * @return array
     */
    public static function getAllTranslations()
    {
        if (!self::$_all_phrases) {
            $translations = self::find()
                ->alias('tr')
                ->select(['tr.phrase', 'trr.name as translation'])
                ->leftJoin('translate_result trr', 'trr.translate_word_id = tr.id AND trr.language_id = "' . (Yii::$app->language ? Yii::$app->language : 'en-US') . '"')
                ->orderBy('tr.phrase ASC')
                ->all();

            foreach ($translations as $item) {
                self::$_all_phrases[$item->phrase] = $item->translation;
            }
        }
        return self::$_all_phrases;
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
