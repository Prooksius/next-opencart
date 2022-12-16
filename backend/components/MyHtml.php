<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 09.02.2019
 * Time: 16:09
 */

namespace backend\components;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\helpers\StringHelper;
use backend\components\InputFileWithDoc;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

class MyHtml extends Html
{
    //---------------------------------------------------------------------
    // Добавление одного или нескольких календарных месяцев к TIMESTAMP
    //---------------------------------------------------------------------
    public static function add_month($time, $num=1) {
        $d=date('j',$time);  // день
        $m=date('n',$time);  // месяц
        $y=date('Y',$time);  // год

        // Прибавить месяц(ы)
        $m+=$num;
        if ($m>12) {
            $y+=floor($m/12);
            $m=($m%12);
            // Дополнительная проверка на декабрь
            if (!$m) {
                $m=12;
                $y--;
            }
        }

        // Это последний день месяца?
        if ($d==date('t',$time)) {
            $d=31;
        }
        // Открутить дату, пока она не станет корректной
        while(true) {
            if (checkdate($m,$d,$y)){
                break;
            }
            $d--;
        }
        // Вернуть новую дату в TIMESTAMP
        return mktime(0,0,0,$m,$d,$y);
    }

    public static function formGroup($group, $name, $label, $value, $options = []) {

        if ($name === null || $name === false) {
            return '';
        }

        if (strpos($name, '[') !== false) {
          $arr = explode('[', $name);
          $first_param = array_shift($arr);
          $last_params = rtrim(implode('[', $arr), ']');
          $name = $first_param . '][' . $last_params;
        }

        $value = (isset($value) && !empty($value) ? $value : '');

        $html  = '<div class="form-group"' . static::renderTagAttributes($options) . '>';
        $html .=    '<label class="control-label" for="'.$group.'-'.$name.'">'.$label.'</label>';
        $html .= '<input type="text" id="' . $group . '-' . $name . '" class="form-control" name="' . $group . '[' . $name . ']" value="' . $value . '" />';
        $html .= '</div>';
        return $html;

    }

    public static function formGroupCheckbox($group, $name, $label, $value, $checked, $options = []) {

        if ($name === null || $name === false) {
            return '';
        }

        if (strpos($name, '[') !== false) {
          $arr = explode('[', $name);
          $first_param = array_shift($arr);
          $last_params = rtrim(implode('[', $arr), ']');
          $name = $first_param . '][' . $last_params;
        }

        $value = (isset($value) && !empty($value) ? $value : '');

        $html  = '<div class="form-group"' . static::renderTagAttributes($options) . '>';
        $html .=  '<label class="control-label">';
        $html .=    '<input type="hidden" name="' . $group . '[' . $name . ']" value="0" />';
        $html .=    '<input type="checkbox" name="' . $group . '[' . $name . ']" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' />';
        $html .=  '&nbsp;' . $label . '</label>';
        $html .= '</div>';
        return $html;

    }

    public static function formGroupCheckboxMult($group, $name, $label, $value, $checked, $options = []) {

        if ($name === null || $name === false) {
            return '';
        }

        if (strpos($name, '[') !== false) {
          $arr = explode('[', $name);
          $first_param = array_shift($arr);
          $last_params = rtrim(implode('[', $arr), ']');
          $name = $first_param . '][' . $last_params;
        }

        $value = (isset($value) && !empty($value) ? $value : '');

        $html  = '<div class="form-group"' . static::renderTagAttributes($options) . '>';
        $html .=  '<label class="control-label">';
        $html .=    '<input type="checkbox" name="' . $group . '[' . $name . '][]" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' />';
        $html .=  '&nbsp;' . $label . '</label>';
        $html .= '</div>';
        return $html;

    }

    public static function formLangGroup($group, $name, $label, $value, $locale, $options = []) {

        if ($name === null || $name === false) {
            return '';
        }

        if (strpos($name, '[') !== false) {
          $arr = explode('[', $name);
          $first_param = array_shift($arr);
          $last_params = rtrim(implode('[', $arr), ']');
          $name = $first_param . '][' . $last_params;
        }

        $value = (isset($value) && !empty($value) ? $value : '');

        $html  = '<div class="form-group"' . static::renderTagAttributes($options) . '>';
        $html .=    '<label class="control-label" for="'.$group.'-'.$name.'-'.$locale.'">'.$label.'</label>';
        $html .= '<input type="text" id="'.$group.'-'.$name.'-'.$locale.'" class="form-control" name="'.$group.'['.$name.']['.$locale.']" value="'.$value.'" />';
        $html .= '</div>';
        return $html;

    }

    public static function formGroupLang($group, $name, $label, $value, $options = [], $languages) {

        if ($name === null || $name === false) {
            return '';
        }

        if (strpos($name, '[') !== false) {
          $arr = explode('[', $name);
          $first_param = array_shift($arr);
          $last_params = rtrim(implode('[', $arr), ']');
          $name = $first_param . '][' . $last_params;
        }

        $value = (isset($value) && !empty($value) ? $value : '');

        $html  = '<div class="form-group"' . static::renderTagAttributes($options) . '>';
        $html .= '  <label class="control-label" for="'.$group.'-'.$name.'">'.$label.'</label>';
        foreach ($languages as $language) {
            $lang_val = isset($value[$language->locale]) ? $value[$language->locale] : '';
            $html .= '  <div class="input-group">';
            $html .= '    <span class="input-group-addon" style="padding-top: 0;padding-bottom: 0;">' . Html::tag('span', '', ['class' => 'flag inline flag-' . $language->code]) . '</span>';
            $html .= '    <input type="text" id="'.$group.'-'.$name.'" class="form-control" name="'.$group.'['.$name.']['.$language->locale.']" value="'.$lang_val.'" />';
            $html .= '  </div>';
        }
        $html .= '</div>';
        return $html;

    }

    public static function formGroupLangArea($model, $group, $name, $label, $value, $options = [], $languages) {

        if ($name === null || $name === false) {
            return '';
        }
        $value = (isset($value) && !empty($value) ? $value : '');

        $html = '  <label class="control-label" for="'.$group.'-'.$name.'">'.$label.'</label>';
        foreach ($languages as $language) {
        $html  .= '<div class="form-group' . (!empty($model->errors[$name][0][$language->locale]) ? ' has-error' : '') . '"' . static::renderTagAttributes($options) . '>';
            $lang_val = isset($value[$language->locale]) ? $value[$language->locale] : '';
            $html .= '  <div class="input-group">';
            $html .= '    <span class="input-group-addon" style="padding-top: 0;padding-bottom: 0;">' . Html::tag('span', '', ['class' => 'flag inline flag-' . $language->code]) . '</span>';
            $html .= '    <textarea id="' . $name . '" rows="7" class="form-control" name="' . $group . '[' . $name . '][' . $language->locale . ']">' . $lang_val . '</textarea>';
            $html .= '  </div>';
            if (!empty($model->errors[$name][0][$language->locale])) {
              $html .= '<div class="help-block">' . $model->errors[$name][0][$language->locale] . '</div>';
            }
            $html .= '</div>';
        }
        return $html;

    }

    public static function formGroupSelect($group, $name, $label, $sel_value, $values, $options = []) {

        if ($name === null || $name === false) {
            return '';
        }

        if (strpos($name, '[') !== false) {
          $arr = explode('[', $name);
          $first_param = array_shift($arr);
          $last_params = implode('[', $arr);
          $name = $first_param . '][' . $last_params;
        }

        $value = (isset($value) && !empty($value) ? $value : '');
        $prompt = (isset($options['prompt']) ? $options['prompt'] : '');

        $html  = '<div class="form-group"' . static::renderTagAttributes($options) . '>';
        $html .=    '<label class="control-label" for="'.$group.'-'.$name.'">'.$label.'</label>';
        if ($prompt) {
            $html .= static::dropDownList($group . '[' . $name . ']', $sel_value, $values, ['class' => 'form-control', 'prompt' => $prompt]);
        } else {
            $html .= static::dropDownList($group . '[' . $name . ']', $sel_value, $values, ['class' => 'form-control']);
        }
        $html .= '</div>';
        return $html;

    }

    public static function languageTabs($model, $languages, $attributes = [])
    {
        $lang_tabs = [];

        $errors = $model->getFirstErrors();
        if (isset($errors['languages'])) {
          $errors = $errors['languages'];
        }

        $errorShown = false;
        foreach ($languages as $language) {

            $active = false;
            if (!empty($errors)) {
              if (!empty($errors[$language->locale]) && !$errorShown) {
                $active = true;
                $errorShown = true;
              } else {
                $active = false;
              }
            } else {
              $active = \Yii::$app->language == $language->locale ? true : false;
            }

            $content = '';
            foreach ($attributes as $attribute => $format) {
                if ($format == 'text') {
                    $content .= MyHtml::languageTextFormGroup($model, $language, $attribute);
                } elseif ($format == 'textarea') {
                    $content .= MyHtml::languageTextareaFormGroup($model, $language, $attribute);
                } elseif ($format == 'file') {
                    $content .= MyHtml::languageFileFormGroup($model, $language, $attribute);
                } elseif ($format == 'CKEditor') {
                    $content .= MyHtml::languageCKEditorFormGroup($model, $language, $attribute);
                } elseif ($format == 'CKEditor_medium') {
                    $content .= MyHtml::languageCKEditorFormGroup($model, $language, $attribute, 300);
                } elseif ($format == 'CKEditor_bigger') {
                    $content .= MyHtml::languageCKEditorFormGroup($model, $language, $attribute, 600);
                } elseif ($format == 'CKEditor_big') {
                    $content .= MyHtml::languageCKEditorFormGroup($model, $language, $attribute, 1000);
                }
            }
            $lang_tabs[] = [
                'label' => Html::tag('span', '', ['class' => 'flag inline flag-' . $language->code]) . ' ' . $language->name,
                'encode' => false,
                'active' => $active,
                'content' => $content,
            ];
        }

        return $lang_tabs;
    }

    public static function languageTextFormGroup($model, $language, $attribute)
    {
        $input_opts = ['class' => 'form-control', 'id' => StringHelper::basename(get_class($model)).'-'. $attribute . '-'  . $language->name];
        if ($attribute == 'name') {
            $input_opts['class'] = "form-control seosource-input";
        }

        $errors = $model->getFirstErrors();
        if (isset($errors['languages'])) {
          $errors = $errors['languages'];
        }

        return Html::tag('div', 
          Html::label($model->getAttributeLabel($attribute)).
            Html::textInput(StringHelper::basename(get_class($model)).'[languages][' . $language->locale . ']['.$attribute.']', 
          $model->languages[$language->locale][$attribute], $input_opts).
            (!empty($errors[$language->locale][$attribute]) ? Html::tag('div', $errors[$language->locale][$attribute], ['class' => 'help-block']) : ''), 
          ['class' => 'form-group' . (!empty($errors[$language->locale][$attribute]) ? ' has-error' : '')]
        );
    }

    public static function languageTextareaFormGroup($model, $language, $attribute)
    {
        $errors = $model->getFirstErrors();
        if (isset($errors['languages'])) {
          $errors = $errors['languages'];
        }

        return Html::tag(
          'div', 
          Html::label($model->getAttributeLabel($attribute)).
            Html::textarea(StringHelper::basename(get_class($model)).'[languages][' . $language->locale . ']['.$attribute.']', 
          $model->languages[$language->locale][$attribute], 
          ['class' => 'form-control', 'id' => StringHelper::basename(get_class($model)).'-'. $attribute . '-'  . $language->name, 'rows' => 5]).
            (!empty($errors[$language->locale][$attribute]) ? Html::tag('div', $errors[$language->locale][$attribute], ['class' => 'help-block']) : ''), 
          ['class' => 'form-group' . (!empty($errors[$language->locale][$attribute]) ? ' has-error' : '')]
        );
    }

    public static function languageFileFormGroup($model, $language, $attribute)
    {

        $errors = $model->getFirstErrors();
        if (isset($errors['languages'])) {
          $errors = $errors['languages'];
        }

        return Html::tag('div',
            Html::label(
                $model->getAttributeLabel($attribute)).
            InputFileWithDoc::widget([
                'language'     => explode('-', \Yii::$app->language)[0],
                'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
                'filter'        => '',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                'template'      => '<div class="document-div"><div class="picture">{picture}</div>{input}<div class="btn-group">{browse_button}{watch_button}{clear_button}</div></div>',
                'options'       => ['class' => 'form-control', 'id' => 'agreement-document-' . $language->locale],
                'buttonName'    => '<span class="glyphicon glyphicon-pencil"></span>',
                'clearButtonName'    => '<span class="glyphicon glyphicon-erase"></span>',
                'watchButtonName'    => '<span class="glyphicon glyphicon-eye-open"></span>',
                'buttonOptions' => ['class' => 'btn btn-primary', 'title' => YII::t('app', 'Choose document')],
                'clearButtonOptions' => ['class' => 'btn btn-warning', 'title' => YII::t('app', 'Clear')],
                'watchButtonOptions' => ['class' => 'btn btn-success', 'title' => YII::t('app', 'Look')],
                'multiple'      => false,       // возможность выбора нескольких файлов
                'name'       => StringHelper::basename(get_class($model)).'[languages][' . $language->locale . ']['.$attribute.']',
                'value'      => $model->languages[$language->locale][$attribute].
              (!empty($errors[$language->locale][$attribute]) ? Html::tag('div', $errors[$language->locale][$attribute], ['class' => 'help-block']) : ''),
            ]),
            ['class' => 'form-group' . (!empty($errors[$language->locale][$attribute]) ? ' has-error' : '')]);
    }

    public static function languageCKEditorFormGroup($model, $language, $attribute, $height = 200)
    {
        $errors = $model->getFirstErrors();
        if (isset($errors['languages'])) {
          $errors = $errors['languages'];
        }

        $editor_options = ElFinder::ckeditorOptions(['elfinder', 'path' => 'image']);
        $editor_options['preset'] = '';
        $editor_options['height'] = $height;
        $editor_options['language'] = explode('-', \Yii::$app->language)[0];
        $editor_options['extraPlugins'] = 'basewidget,layoutmanager,triggers,youtube';
        $editor_options['layoutmanager_loadbootstrap'] = true;

        return Html::tag(
          'div', 
          Html::label($model->getAttributeLabel($attribute)) . 
            CKEditor::widget(['name' => StringHelper::basename(get_class($model)).'[languages][' . $language->locale . ']['.$attribute.']', 
              'value' => $model->languages[$language['locale']][$attribute], 'editorOptions' => $editor_options]).
              (!empty($errors[$language->locale][$attribute]) ? Html::tag('div', $errors[$language->locale][$attribute], ['class' => 'help-block']) : ''), 
          ['class' => 'form-group' . (!empty($errors[$language->locale][$attribute]) ? ' has-error' : '')]
        );
    }

    public static function percent($group, $name, $label, $value, $min = 0, $max = 100, $options = []) {

        if ($name === null || $name === false) {
            return '';
        }
        $value = (isset($value) && !empty($value) ? $value : '');

        $html  = '<div class="form-group"' . static::renderTagAttributes($options) . '>';
        $html .=    '<label class="control-label" for="'.$group.'-'.$name.'">'.$label.'</label>';
        $html .= '<input type="number" id="' . $group . '-' . $name . '" class="form-control" name="' . $group . '[' . $name . ']" value="' . $value . '" min="' . $min . '" max="' . $max . '" />';
        $html .= '</div>';
        return $html;

    }
    public static function activeCheckboxTreeInput($model, $selection_attribute, $treeQuery, $idAttribute, $labelAttribute, $options = [])
    {
        $items = self::prepareTreeItems($treeQuery, $idAttribute, $labelAttribute);
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $selection_attribute);
        $selection = static::getAttributeValue($model, $selection_attribute);
        if (!array_key_exists('unselect', $options)) {
            $options['unselect'] = '';
        }
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $selection_attribute);
        }

        if (substr($name, -2) !== '[]') {
            $name .= '[]';
        }
        if (ArrayHelper::isTraversable($selection)) {
            $selection = array_map('strval', (array)$selection);
        }

        $formatter = ArrayHelper::remove($options, 'item');
        $itemOptions = ArrayHelper::remove($options, 'itemOptions', []);
        $encode = ArrayHelper::remove($options, 'encode', true);
        $separator = ArrayHelper::remove($options, 'separator', "<br>");
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        $visibleContent  = Html::beginTag('div', ['class' => 'checkbox-tree-cont']);
        $visibleContent .= self::renderCheckboxTreeItems($items, $name, $selection, $formatter, $itemOptions, $encode, $separator) . $separator;
        $visibleContent .= Html::endTag('div');

        if (isset($options['unselect'])) {
            // add a hidden field so that if the list box has no option being selected, it still submits a value
            $name2 = substr($name, -2) === '[]' ? substr($name, 0, -2) : $name;
            $hidden = static::hiddenInput($name2, $options['unselect']);
            unset($options['unselect']);
        } else {
            $hidden = '';
        }

        if ($tag === false) {
            return $hidden . $visibleContent;
        }

        return $hidden . static::tag($tag, $visibleContent, $options);

    }

    protected static function renderCheckboxTreeItems($_items, $name, $selection, $formatter, $itemOptions, $encode, $separator) {

        $lines = '';

        foreach ($_items as $item) {

            $value = $item['id'];
            $label = $item['content'];

            $checked = $selection !== null &&
                (!ArrayHelper::isTraversable($selection) && !strcmp($value, $selection)
                    || ArrayHelper::isTraversable($selection) && ArrayHelper::isIn((string)$value, $selection));
            if ($formatter !== null) {
                $lines .= call_user_func($formatter, $label, $name, $checked, $value);
            } else {
                $lines .= static::checkbox($name, $checked, array_merge($itemOptions, [
                    'value' => $value,
                    'label' => $encode ? static::encode($label) : $label,
                ]));
            }
            $lines .= $separator;

            $children = $item['children'];
            if (!empty($children)) {
                // recursive rendering children items
                $content  = Html::beginTag('div', ['class' => 'children-group']);
                $content .= self::renderCheckboxTreeItems($children, $name, $selection, $formatter, $itemOptions, $encode, $separator);
                $content .= Html::endTag('div');
                $lines .= $content;
            }
        }

        return $lines;
    }



    protected static function prepareTreeItems($activeQuery, $idAttribute, $labelAttribute)
    {
        $items = [];
        foreach ($activeQuery->all() as $model) {
            $items[] = [
                'id'       => $model->{$idAttribute},
                'content'  => $model->{$labelAttribute},
                'children' => self::prepareTreeItems($model->children(1), $idAttribute, $labelAttribute),
            ];
        }
        return $items;
    }
}