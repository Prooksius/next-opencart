<?php

namespace frontend\models;

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
    public $image;
    public $bg_image;
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
            [['page_id'], 'integer'],
            [['text'], 'string'],
            [['language_id'], 'string'],
            [['name', 'title', 'subtitle'], 'string'],
            [['page_title', 'page_kwords'], 'string'],
            [['page_desc'], 'string'],
        ];
    }
}