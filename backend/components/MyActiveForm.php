<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 19.01.2019
 * Time: 20:40
 */

namespace backend\components;

use Yii;
use yii\widgets\ActiveFormAsset;
use yii\bootstrap\ActiveForm;
use yii\web\View;
//use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

class MyActiveForm extends ActiveForm {

    public function registerClientScript() {
        $id = $this->options['id'];
        $view = $this->getView();
        $jscript = "var submit_btn = '';
        if ($('#".$id." [type=\"submit\"]').length) {
            submit_btn = $('#".$id." [type=\"submit\"]');
        } else {
            submit_btn = $('#".$this->options['submit_button_id']."');
        };
        submit_btn.on('click', function() {
                var cur_form = $('#".$id."');
                cur_form.find('input[type=\"text\"], input[type=\"checkbox\"]:checked, input[type=\"radio\"]:checked, textarea, select').each(function() {
                    if ($(this).val() === '') $(this).attr('disabled', 'disabled');
                });
                cur_form.submit();
                return false;
        });";

        $view->registerJs( $jscript, View::POS_READY);

        parent::registerClientScript();
    }

    public function field($model, $attribute, $options = []) {
        $this->fieldClass = 'yii\bootstrap\ActiveField';
        if (!isset($options['inputOptions']['name'])) {
            $options['inputOptions']['name'] = 'field-'.$attribute;
        }
        return parent::field($model, $attribute, $options);
    }
}