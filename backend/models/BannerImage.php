<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_banner_image".
 */
class BannerImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_banner_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['banner_id', 'language_id'], 'required'],
            [['banner_id', 'sort_order'], 'integer'],
            [['language_id'], 'string', 'max' => 10],
            [['title', 'text1', 'text2' , 'text3'], 'string', 'max' => 255],
            [['link', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'banner_id' => YII::t('app', 'Banner'),
            'title' => YII::t('app', 'Title'),
            'text1' => YII::t('app', 'Text1'),
            'text2' => YII::t('app', 'Text2'),
            'text3' => YII::t('app', 'Text3'),
            'image' => YII::t('app', 'Image'),
            'link' => YII::t('app', 'Link'),
            'status' => YII::t('app', 'Status'),
            'sort_order' => YII::t('app', 'Sort Order'),
        ];
    }
}
