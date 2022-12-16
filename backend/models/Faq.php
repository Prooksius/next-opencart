<?php

namespace app\models;

use Yii;
use backend\components\MyActiveRecord;

/**
 * This is the model class for table "faq".
 *
 * @property int $id
 * @property string $picture
 * @property string $status
 */
class Faq extends MyActiveRecord
{
    protected $_desc_class = '\app\models\FaqDesc';
    protected $_desc_id_name = 'faq_id';
    protected $_desc_fields = ['name', 'description'];

    public $name;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_faq';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['languages'], 'safe'],
            [['status', 'sort_order'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => YII::t('app', 'ID'),
            'name' => YII::t('app', 'Name'),
            'description' => YII::t('app', 'Description'),
            'status' => YII::t('app', 'Active?'),
            'sort_order' => YII::t('app', 'Sort Order'),
        ];
    }
}