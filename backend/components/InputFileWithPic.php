<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 26.01.2019
 * Time: 23:27
 */

namespace backend\components;

use Yii;
use mihaildev\elfinder\InputFile;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use mihaildev\elfinder\AssetsCallBack;
use himiklab\thumbnail\EasyThumbnailImage;

class InputFileWithPic extends InputFile {
    public $buttonName = 'Выбрать';
    public $clearButtonName = 'Очистить';
    public $deleteButtonName = 'Удалить';
    public $clearButtonOptions = [];
    public $deleteButtonOptions = [];
    public function init() {
        parent::init();
        $this->buttonOptions['id'] = $this->options['id'].'_button';
        $this->clearButtonOptions['id'] = $this->options['id'].'_clear';
        $this->clearButtonOptions['type'] = 'button';
        $this->deleteButtonOptions['id'] = $this->options['id'].'_delete';
        $this->deleteButtonOptions['type'] = 'button';

    }
    /**
     * Runs the widget.
     */
    public function run() {
        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::hiddenInput($this->name, $this->value, $this->options);
        }

        if(empty($this->buttonOptions['class'])) {
            $this->buttonOptions['class'] = 'btn-browse-file';
        } else {
            $this->buttonOptions['class'] .= ' btn-browse-file';
        }
        if(empty($this->clearButtonOptions['class'])) {
            $this->clearButtonOptions['class'] = 'btn-clear-file';
        } else {
            $this->clearButtonOptions['class'] .= ' btn-clear-file';
        }
        if(empty($this->deleteButtonOptions['class'])) {
            $this->deleteButtonOptions['class'] = 'btn-delete-file';
        } else {
            $this->deleteButtonOptions['class'] .= ' btn-delete-file';
        }

        $replace['{browse_button}'] = Html::tag($this->buttonTag,$this->buttonName, $this->buttonOptions);
        $replace['{clear_button}'] = Html::tag($this->buttonTag,$this->clearButtonName, $this->clearButtonOptions);
        $replace['{delete_button}'] = Html::tag($this->buttonTag,$this->deleteButtonName, $this->deleteButtonOptions);

        if ($this->hasModel()) {
            $replace['{picture}'] = Html::img($this->model->{$this->attribute}, ['id' => $this->options['id'] . '-image', 'class' => 'img-responsive']);
        } else {
            $ext = pathinfo($this->value)['extension'];
            if ($this->value && $ext != 'svg') {
                $replace['{picture}'] = EasyThumbnailImage::thumbnailImg(
                    '@root' . $this->value,
                    200,
                    200,
                    EasyThumbnailImage::THUMBNAIL_INSET,
                    ['id' =>  $this->options['id'] . '-image', 'class' => 'img-responsive']
                    );

//                    Html::img($this->value, ['id' => $this->options['id'] . '-image', 'class' => 'img-responsive']);
            } else if ($this->value && $ext == 'svg'){
                $replace['{picture}'] = Html::img($this->value, ['id' => $this->options['id'] . '-image', 'class' => 'img-responsive']);
            } else {
                $replace['{picture}'] = Html::img('/backend/components/placeholder.png', ['id' => $this->options['id'] . '-image', 'class' => 'img-responsive']);
            }
        }

        echo strtr($this->template, $replace);

        AssetsCallBack::register($this->getView());

        if (!empty($this->multiple))
            $this->getView()->registerJs("mihaildev.elFinder.register(" . Json::encode($this->options['id']) . ", function(files, id){ var _f = []; for (var i in files) { _f.push(files[i].url); } \$('#' + id).val(_f.join(', ')).trigger('change', [files, id]); return true;}); $(document).on('click','#" . $this->buttonOptions['id'] . "', function(){mihaildev.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");
        else
            $this->getView()->registerJs("mihaildev.elFinder.register(" . Json::encode($this->options['id']) . ", function(file, id){ \$('#' + id).val(file.url).trigger('change', [file, id]); \$('#' + id + '-image').attr('src', file.url); return true;}); $(document).on('click', '#" . $this->clearButtonOptions['id'] . "', function(){ \$('#" . $this->options['id'] . "').val(''); \$('#" . $this->options['id'] . "').trigger('change'); \$('#" . $this->options['id'] . "' + '-image').attr('src', '/backend/components/placeholder.png'); return true }); \$(document).on('click', '#" . $this->buttonOptions['id'] . "', function(){mihaildev.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");
    }

}