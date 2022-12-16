<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 30.04.2020
 * Time: 8:27
 */

namespace frontend\models;

use common\models\Language;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class LanguageChoose extends Model
{
    private static $_all_langs;

    /**
     * @var string language attribute
     */
    public $language;
    public $redirect;

    public static function getAllLanguages()
    {
        if (!self::$_all_langs) {
            self::$_all_langs = Language::find()
                ->select(['locale', 'name'])
                ->orderBy('name ASC')
				->asArray()
                ->all();
        }
		foreach (self::$_all_langs as &$_lang) {
			$_lang['code'] = explode('-', $_lang['locale'])[0];
			$_lang['selected'] = $_lang['locale'] == Yii::$app->language;
		}
		
        return self::$_all_langs;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'language' => YII::t('app', 'Language'),
        ];
    }
}