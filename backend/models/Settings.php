<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "settings".
 *
 * @property string $setting_group
 * @property string $setting_name
 * @property string $setting_value
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
    public function rules()
    {
        return [
            [['setting_group', 'setting_name', 'setting_value'], 'required'],
            [['setting_value'], 'string'],
            [['serialized'], 'number'],
            [['setting_group', 'setting_name'], 'string', 'max' => 50],
            [['setting_group', 'setting_name'], 'unique', 'targetAttribute' => ['setting_group', 'setting_name']],
        ];
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

    /**
     * {@inheritdoc}
     */
    public static function saveAllSettings($values, $setting_group = 'general', $exclude_groups = []) {

        if ($setting_group) {

            self::deleteAll(['setting_group' => $setting_group]);

            foreach ($values[$setting_group] as $key => $value) {
                $curSetting = new Settings;
                $curSetting->setting_group = $setting_group;
                $curSetting->setting_name = $key;
                if (is_array($value)) {
                    $curSetting->setting_value = Json::encode($value);
                    $curSetting->serialized = 1;
                } else {
                    $curSetting->setting_value = $value;
                    $curSetting->serialized = 0;
                }
            $curSetting->save();
            }
        } else {

            if (!empty($exclude_groups)) {
                self::deleteAll(['NOT IN', 'setting_group', $exclude_groups]);
            } else {
                self::deleteAll();
            }

            foreach ($values as $setting_group => $setting_group_content) {
                if ($setting_group  == '_csrf-backend') {
                    continue;
                }
                foreach ($setting_group_content as $key => $value) {
                    $curSetting = new Settings;
                    $curSetting->setting_group = $setting_group;
                    $curSetting->setting_name = $key;
                    if (is_array($value)) {
                        $curSetting->setting_value = Json::encode($value);
                        $curSetting->serialized = 1;
                    } else {
                        $curSetting->setting_value = $value;
                        $curSetting->serialized = 0;
                    }
                $curSetting->save();
                }
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public static function saveSetting($setting_name, $setting_value, $setting_group = 'general') {

        $model = self::find()
                    ->where(['setting_group' => $setting_group, 'setting_name' => $setting_name])
                    ->one();

        if (is_array($value)) {
            $model->setting_value = Json::encode($value);
            $model->serialized = 1;
        } else {
            $model->setting_value = $setting_value;
            $model->serialized = 0;
        }
        $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'setting_group' => 'Setting Group',
            'setting_name' => 'Setting Name',
            'setting_value' => 'Setting Value',
        ];
    }
}
