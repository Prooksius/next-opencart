<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "faq".
 *
 * @property int $id
 * @property string $link
 */
class Faq extends \yii\db\ActiveRecord
{
    private static $_all_faqs;
    public $name;
    public $description;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faq';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort_order', 'status'], 'integer'],
            [['image'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public static function getAllFaqs()
    {
        if (!self::$_all_faqs) {
            $faqs = self::find()
                ->alias('f')
                ->select(['f.id', 'fd.*'])
                ->leftJoin('faq_desc fd', 'fd.faq_id = f.id AND fd.language_id = "' . \Yii::$app->language . '"')
                ->where(['f.status' => 1])
                ->orderBy('f.sort_order ASC')
                ->all();

            foreach ($faqs as $item) {
                self::$_all_faqs[] = [
                    'id' => $item->id,
                    'text' => $item->name,
                    'description' => $item->description,
                    'image' => $item->image,
                ];
            }
        }
        return self::$_all_faqs;
    }
}
