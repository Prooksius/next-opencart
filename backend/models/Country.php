<?php

namespace app\models;

use backend\components\MyActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $link
 */
class Country extends MyActiveRecord
{
    protected $_desc_class = '\app\models\CountryDesc';
    protected $_desc_id_name = 'country_id';
    protected $_desc_fields = ['name', 'title'];

    public $name;

    private static $_all_countries;
    private static $_all_countries_text;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flag'], 'string', 'max' => 255],
            [['iso_code_2'], 'string', 'max' => 2],
            [['iso_code_3'], 'string', 'max' => 3],
            [['phonemask'], 'string', 'max' => 50],
            [['popularity', 'status'], 'integer'],
            [['popularity'], 'default', 'value' => 0],
            [['languages', 'name'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public static function getAllAcountries()
    {
        if (!static::$_all_countries) {
            static::$_all_countries = ArrayHelper::map(
                static::find()
                    ->alias('c')
                    ->select('c.id, cd.name')
                    ->leftJoin('country_desc cd', '(cd.country_id = c.id AND cd.language_id = "' . \Yii::$app->language . '")')
                    ->orderBy([
                        'c.popularity' => SORT_DESC,
                        'cd.name' => SORT_ASC
                    ])
                    ->all(),
                'id', 'name');
        }
        return static::$_all_countries;
    }

    /**
     * @return array
     */
    public static function getAllCountriesText()
    {
        if (!static::$_all_countries_text) {
            static::$_all_countries_text = ArrayHelper::map(
                static::find()
                    ->alias('c')
                    ->select('cd.name')
                    ->leftJoin('country_desc cd', '(cd.country_id = c.id AND cd.language_id = "' . \Yii::$app->language . '")')
                    ->orderBy([
                        'c.popularity' => SORT_DESC,
                        'cd.name' => SORT_ASC
                    ])
                    ->all(),
                'name', 'name');
        }
        return static::$_all_countries_text;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => YII::t('app', 'ID'),
            'flag' => YII::t('app', 'Flag'),
            'iso_code_2' => YII::t('app', 'ISO code 2'),
            'iso_code_3' => YII::t('app', 'ISO code 3'),
            'phonemask' => YII::t('app', 'Phone mask'),
            'popularity' => YII::t('app', 'Popularity'),
            'status' => YII::t('app', 'Status'),
            'name' => YII::t('app', 'Name'),
            'title' => YII::t('app', 'Title'),
            'sef' => YII::t('app', 'SEO address'),
        ];
    }
}
