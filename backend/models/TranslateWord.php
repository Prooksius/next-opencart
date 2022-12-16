<?php

namespace app\models;

use Yii;
use backend\components\MyActiveRecord;

/**
 * This is the model class for table "translate_word".
 *
 * @property int $id
 * @property string $picture
 * @property string $status
 */
class TranslateWord extends MyActiveRecord
{
    protected $_desc_class = '\app\models\TranslateResult';
    protected $_desc_id_name = 'translate_word_id';
    protected $_desc_fields = ['name'];

    public $name;
    public $group;
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
            [['phrase'], 'string', 'max' => 255],
            ['translate_group_id', 'integer'],
            [['phrase'], 'required'],
            [['phrase'], 'unique'],
            [['languages'], 'safe'],
        ];
    }

    public function getTranslateGroupsList()
    {
      return TranslateGroup::getAllGroups();
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

        if ($translation instanceof TranslateWord) {
            return $translation->translation;
        } else {
            return $phrase;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => YII::t('app', 'ID'),
            'phrase' => YII::t('app', 'Phrase'),
            'name' => YII::t('app', 'Translation'),
            'translate_group_id' => YII::t('localisation', 'Translation group'),
        ];
    }
}