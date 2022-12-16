<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "settings".
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public static function readAllSettings($setting_group = 'general') {

        $settings_arr = [];

        if ($setting_group) {
            $allSettings = self::find()->where(['setting_group' => $setting_group])->all();

            foreach ($allSettings as $allSetting) {
                if ($allSetting->serialized) {
                    $settings_arr[$allSetting->setting_name] = Json::decode($allSetting->setting_value, true);
                } else {
                    $settings_arr[$allSetting->setting_name] = $allSetting->setting_value;
                }
            }
        } else {
            $allSettings = self::find()->all();

            foreach ($allSettings as $allSetting) {
                if ($allSetting->serialized) {
                    $settings_arr[$allSetting->setting_group][$allSetting->setting_name] = Json::decode($allSetting->setting_value, true);
                } else {
                    $settings_arr[$allSetting->setting_group][$allSetting->setting_name] = $allSetting->setting_value;
                }
            }
        }

        return $settings_arr;
    }

    /**
     * {@inheritdoc}
     */
    public static function readSetting($setting_name, $setting_group = 'general') {

        $temp_value = self::find()
            ->where(['setting_group' => $setting_group, 'setting_name' => $setting_name])
            ->one();

        if ($temp_value->serialized) {
            return Json::decode($temp_value->setting_value, true);
        } else {
            return $temp_value->setting_value;
        }
    }
}
