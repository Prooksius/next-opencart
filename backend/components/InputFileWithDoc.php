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

class InputFileWithDoc extends InputFile {
    public $buttonName = 'Выбрать';
    public $clearButtonName = 'Очистить';
    public $deleteButtonName = 'Удалить';
    public $watchButtonName = 'Посмотреть';
    public $clearButtonOptions = [];
    public $deleteButtonOptions = [];
    public $watchButtonOptions = [];
    private $base;
    public function init() {
        parent::init();
        $this->buttonOptions['id'] = $this->options['id'].'_button';
        $this->clearButtonOptions['id'] = $this->options['id'].'_clear';
        $this->clearButtonOptions['type'] = 'button';
        $this->deleteButtonOptions['id'] = $this->options['id'].'_delete';
        $this->deleteButtonOptions['type'] = 'button';
        $this->watchButtonOptions['id'] = $this->options['id'].'_watch';
        $this->watchButtonOptions['type'] = 'button';
        $this->base = Yii::$app->request->hostInfo;
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
        if(empty($this->watchButtonOptions['class'])) {
            $this->watchButtonOptions['class'] = 'btn-watch-file';
        } else {
            $this->watchButtonOptions['class'] .= ' btn-watch-file';
        }
        if(empty($this->deleteButtonOptions['class'])) {
            $this->deleteButtonOptions['class'] = 'btn-delete-file';
        } else {
            $this->deleteButtonOptions['class'] .= ' btn-delete-file';
        }

        $replace['{browse_button}'] = Html::tag($this->buttonTag,$this->buttonName, $this->buttonOptions);
        $replace['{clear_button}'] = Html::tag($this->buttonTag,$this->clearButtonName, $this->clearButtonOptions);
        $replace['{delete_button}'] = Html::tag($this->buttonTag,$this->deleteButtonName, $this->deleteButtonOptions);
        $replace['{watch_button}'] = Html::tag($this->buttonTag,$this->watchButtonName, $this->watchButtonOptions);

        if ($this->hasModel()) {
            $value = $this->model->{$this->attribute};
        } else {
            $value = $this->value;
        }

        if ($value) {
            $basename = urldecode(pathinfo($value)['basename']);
        } else {
            $basename = '- файл не выбран -';
        }

        if ($value) {
            $ext = 'application';
        } else {
            $ext = 'none';
        }

        $replace['{picture}'] = Html::tag('span', $basename, ['id' => $this->options['id'] . '-document']);

        echo strtr($this->template, $replace);

        AssetsCallBack::register($this->getView());

        if (!empty($this->multiple))
            $this->getView()->registerJs("mihaildev.elFinder.register(" . Json::encode($this->options['id']) . ", function(files, id){ var _f = []; for (var i in files) { _f.push(files[i].url); } \$('#' + id).val(_f.join(', ')).trigger('change', [files, id]); return true;}); $(document).on('click','#" . $this->buttonOptions['id'] . "', function(){mihaildev.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");
        else
            $this->getView()->registerJs("
        
        mihaildev.elFinder.register(" . Json::encode($this->options['id']) . ", function(file, id){ 
            \$('#' + id).val(file.url).trigger('change', [file, id]); 
            \$('#' + id + '-document').text(file.name); 
            return true;
        }); 
        $(document).on('click', '#" . $this->watchButtonOptions['id'] . "', function(){
            var file = \$('#" . $this->options['id'] . "').val();
            if (file) {
                var images = ['png', 'jpg', '']
                var ext = file.slice(file.lastIndexOf('.')+1);
                var fb_type = 'iframe';
                if (ext == 'png' || ext == 'jpg' || ext == 'jpeg' || ext == 'gif') {
                    fb_type = 'image';
                }
                if (ext == 'mp4' || ext == 'mov' || ext == 'avi' || ext == 'mpg' || ext == 'mpeg') {
                    fb_type = 'video';
                }
				console.log('this->base: ' , '" . $this->base . "');
                \$.fancybox.open({
                    src  : '" . $this->base . "' + file,
                    type : fb_type,
                });
            }
        });
        $(document).on('click', '#" . $this->clearButtonOptions['id'] . "', function(){ 
            \$('#" . $this->options['id'] . "').val(''); 
            \$('#" . $this->options['id'] . "').trigger('change'); 
            \$('#" . $this->options['id'] . "' + '-document').text('- файл не выбран -'); 
            return true 
        }); 
            
        \$(document).on('click', '#" . $this->buttonOptions['id'] . "', function(){
            mihaildev.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");
        });
    
        ");
    }

}