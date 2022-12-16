<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_newsblog_article_image".
 */
class BlogArticleImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_newsblog_article_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image'], 'required'],
            [['article_id', 'sort_order'], 'integer'],
            [['image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => YII::t('app', 'ID'),
            'image' => YII::t('app', 'Image'),
            'article_id' => YII::t('blog', 'Blog Article'),
            'sort_order' => YII::t('app', 'Sort Order'),
        ];
    }
}
