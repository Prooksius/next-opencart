<?php

namespace frontend\models;

use frontend\components\Helper;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $link
 */
class Country extends \yii\db\ActiveRecord
{
    private static $_all_cuntries;
    public $country_name;
    public $country_title;
    public $meta_title;
    public $meta_desc;
    public $meta_kwords;
    public $link_sef;
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
            [['flag', 'iso_code_2', 'iso_code_3', 'phonemask'], 'string'],
            [['country_name', 'country_title', 'link_sef'], 'safe'],
            [['id', 'popularity', 'status'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public static function getAllCountries()
    {

        if (!self::$_all_cuntries) {

            self::$_all_cuntries = [];

            $countries = self::find()
                    ->alias('c')
                    ->select([
                        'c.*',
                        'cd.name as country_name',
                    ])
                    ->leftJoin('country_desc cd', 'cd.country_id = c.id AND cd.language_id = "' . \Yii::$app->language . '"')
                    ->where(['c.status' => 1])
                    ->orderBy([
                        'c.popularity' => SORT_DESC,
                        'cd.name' => SORT_ASC
                    ])
                    ->all();

            foreach ($countries as $item) {
                self::$_all_cuntries[] = [
                    'id' => $item->id,
                    'value' => $item->id,
                    'code' => $item->iso_code_2,
                    'text' => $item->country_name,
                    'flag' => $item->flag,
                ];
            }
        }
        return self::$_all_cuntries;
    }

    public static function getPhoneMasks()
    {
        $masks = [];
        $maskData = static::find()
            ->alias('c')
            ->select(['c.iso_code_2', 'c.phonemask'])
            ->leftJoin('oc_country_desc cd', 'cd.country_id = c.id AND cd.language_id = "' . \Yii::$app->language . '"')
            ->where('c.phonemask != ""')
            ->orderBy('c.popularity DESC, cd.name ASC')
            ->all();

        foreach ($maskData as $mask) {
            $masks[$mask->iso_code_2] = $mask->phonemask;
        }

        return $masks;
    }

    public static function getPhoneMasksArr()
    {
        $masks = [];
        $maskData = static::find()
            ->alias('c')
            ->select(['c.iso_code_2', 'c.phonemask', 'c.flag'])
            ->leftJoin('oc_country_desc cd', 'cd.country_id = c.id AND cd.language_id = "' . \Yii::$app->language . '"')
            ->where('c.phonemask != ""')
            ->orderBy('c.popularity DESC, cd.name ASC')
            ->all();

        foreach ($maskData as $mask) {
            $masks[] = [
                'code' => $mask->iso_code_2,
                'mask' => $mask->phonemask,
                'flag' => $mask->flag
            ];
        }

        return $masks;
    }

    public static function getPhoneMaskData($phone)
    {

        if ($phone) {
            $phoneArr = explode(' ', $phone);
            $phoneStart = $phoneArr[0] . ' ';

            $expression = new Expression('phonemask LIKE "' . $phoneStart . '%" DESC');

            $closestMask = static::find()
                ->select(['iso_code_2', 'phonemask'])
                ->where('phonemask != ""')
                ->orderBy($expression)
                ->one();
        } else {
            $closestMask = static::find()
                ->select(['iso_code_2', 'phonemask'])
                ->where(['iso_code_2' => 'RU'])
                ->one();
        }

        return [
            'iso_code_2' => $closestMask->iso_code_2,
            'mask' => $closestMask->phonemask,
        ];
    }

}
