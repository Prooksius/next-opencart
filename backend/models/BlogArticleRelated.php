<?php

namespace app\models;

/**
 * This is the model class for table "oc_newsblog_article_related".
 */
class BlogArticleRelated extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_newsblog_article_related';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_id', 'related_id'], 'required'],
            [['article_id', 'related_id'], 'unique'],
            [['article_id', 'related_id'], 'integer'],
        ];
    }
}
