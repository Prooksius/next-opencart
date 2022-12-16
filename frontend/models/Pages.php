<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property int $id
 * @property string $link
 */
class Pages extends \yii\db\ActiveRecord
{
    public $name;
    public $content;
    public $page_title;
    public $page_desc;
    public $page_kwords;
    public $href;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image'], 'string'],
            [['languages', 'sef', 'name'], 'safe'],
        ];
    }

    public static function getPageByLink($link)
    {
        $sef_count = Sef::find()
            ->where(['link_sef' => $link])
            ->andWhere('link LIKE "site/pages?id=%"')
            ->count();

        $query = PagesDesc::find()
            ->alias('pd')
            ->select(['p.*', 'pd.*'])
            ->leftJoin('pages p', 'p.id = pd.page_id');

        if ((int)$sef_count) {
            $query = $query
                ->leftJoin('sef se', ['se.link_sef' => $link])
                ->where('pd.page_id = CAST(REPLACE(se.link, "site/pages?id=", "") AS UNSIGNED)');
        } else {
            $query = $query
                ->where('pd.page_id NOT IN (SELECT CAST(REPLACE(se.link, "site/pages?id=", "") AS UNSIGNED) FROM sef se WHERE se.link LIKE "site/pages?id=%")');
        }
        $query = $query
            ->andWhere(['pd.language_id' => \Yii::$app->language]);

        return $query->one();
    }

    public static function getPageById($id)
    {
        $query = PagesDesc::find()
            ->alias('pd')
            ->select(['p.*', 'pd.*'])
            ->leftJoin('pages p', 'p.id = pd.page_id')
            ->where(['pd.language_id' => \Yii::$app->language])
            ->andWhere(['pd.page_id' => (int)$id]);

        return $query->one();
    }
}
