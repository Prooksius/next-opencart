<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 10.02.2019
 * Time: 10:15
 */

namespace backend\components;

use Yii;
use mihaildev\elfinder\ElFinder;
use mihaildev\elfinder\InputFile;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use yii\base\InvalidArgumentException;
use mihaildev\elfinder\AssetsCallBack;
use himiklab\thumbnail\EasyThumbnailImage;
use yii\jui\Sortable;

class InputFileGallery extends InputFileWithPic {
    public $id = 'images-sortable';
    public $buttonName = 'Choose';
    public $deleteButtonName = 'Delete';
    public $addButtonName = 'Add image';
    public $deleteButtonOptions = [];
    public $addButtonOptions = [];
    public $filePathName = 'image';
    public $sortOrderName = 'sort_order';
    private $managerOptions2 = [];
    public $label = '';

    public function init() {
        parent::init();

        $this->buttonName = YII::t('app', 'Choose');
        $this->deleteButtonName = YII::t('app', 'Delete');
        $this->addButtonName = YII::t('app', 'Add image');

        $this->managerOptions2 = [];
        if(!empty($this->filter))
            $this->managerOptions2['filter'] = $this->filter;

        $this->managerOptions2['callback'] = $this->options['id'];

        if(!empty($this->language))
            $this->managerOptions2['lang'] = $this->language;

        if (!empty($this->multiple))
            $this->managerOptions2['multiple'] = $this->multiple;

        if(!empty($this->path))
            $this->managerOptions2['path'] = $this->path;

        $this->_managerOptions['width'] = $this->width;
        $this->_managerOptions['height'] = $this->height;

        $this->deleteButtonOptions['type'] = 'button';
        $this->addButtonOptions['type'] = 'button';
        $this->addButtonOptions['id'] = $this->options['id'].'_add';
    }
    /**
     * Runs the widget.
     */
    public function run()
    {

        if (empty($this->buttonOptions['class'])) {
            $this->buttonOptions['class'] = 'btn-browse-file';
        } else {
            $this->buttonOptions['class'] .= ' btn-browse-file';
        }
        if (empty($this->deleteButtonOptions['class'])) {
            $this->deleteButtonOptions['class'] = 'btn-delete-file';
        } else {
            $this->deleteButtonOptions['class'] .= ' btn-delete-file';
        }
        if (empty($this->addButtonOptions['class'])) {
            $this->addButtonOptions['class'] = 'btn-add-file';
        } else {
            $this->addButtonOptions['class'] .= ' btn-add-file';
        }

        $items = [];
        $html = '
        <div class="row">
            <div class="col-sm-12"><hr></div>
            <div class="col-lg-9 col-md-8 col-sm-6">
                <label class="control-label">'.$this->label.'</label>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 text-right">
                '.Html::Button($this->addButtonName, $this->addButtonOptions).'
            </div>
        </div>
        <div class="images-cont">
            <div class="row pimage_row">';

        if ($this->hasModel()) {
            $name = Html::getInputName($this->model, $this->attribute);
            $itemsArray = $this->model->{$this->attribute};
            if (is_string($itemsArray)) {
                try {
                    $itemsArray = Json::decode($itemsArray);
                } catch (InvalidArgumentException $e) {
                    $itemsArray = [];
                }
            }
        } else {
            $name = $this->name;
            $itemsArray = $this->value;
        }
        if (!empty($itemsArray)) {
            $key = 1;
            foreach ($itemsArray as $item) {
                $cur_options = $this->options;
                $cur_options['id'] = $cur_options['id'] . '-' . $key;
                if (empty($cur_options['class'])) {
                    $cur_options['class'] = 'image-path-input';
                } else {
                    $cur_options['class'] .= ' image-path-input';
                }
                $replace['{input}'] = Html::hiddenInput($name . '[' . $key . '][' . $this->filePathName . ']', $item[$this->filePathName], $cur_options);

                $cur_browse_options = $this->buttonOptions;
                $cur_browse_options['id'] = $this->options['id'] . '_button-' . $key;
                $cur_browse_options['data-id'] = $this->options['id'] . '-' . $key;

                $cur_delete_options = $this->deleteButtonOptions;
                $cur_delete_options['id'] = $this->options['id'] . '_delete-' . $key;

                $replace['{browse_button}'] = Html::tag($this->buttonTag, '<span class="glyphicon glyphicon-pencil"></span>', $cur_browse_options);
                $replace['{delete_button}'] = Html::tag($this->buttonTag, '<span class="glyphicon glyphicon-trash"></span>', $cur_delete_options);

                $ext = pathinfo($item[$this->filePathName])['extension'];
                if ($this->filePathName && $ext != 'svg') {
                    $replace['{picture}'] = EasyThumbnailImage::thumbnailImg(
                        '@root' . $item[$this->filePathName],
                        200,
                        200,
                        EasyThumbnailImage::THUMBNAIL_INSET,
                        ['id' => $this->options['id'] . '-' . $key . '-image', 'class' => 'img-responsive']
                    );
                } else if ($this->filePathName && $ext == 'svg'){
                    $replace['{picture}'] = Html::img($item[$this->filePathName], ['id' => $this->options['id'] . '-image', 'class' => 'img-responsive']);
                } else {
                    $replace['{picture}'] = Html::img('/backend/components/placeholder.png', ['id' => $this->options['id'] . '-image', 'class' => 'img-responsive']);
                }

                $items[] = '<div class="picture-div">' .
                    strtr($this->template, $replace) .
                    Html::HiddenInput($name . '[' . $key . '][' . $this->sortOrderName . ']', $item[$this->sortOrderName], ['class' => 'sort-order-input', 'id' => $this->options['id'] . '-sortorder-' . $key]) .
                    '</div>';

                $cur_manager_options = $this->_managerOptions;
                $params = $this->managerOptions2;
                $params['callback'] = $this->options['id'] . '-' . $key;
                if (!empty($this->startPath))
                    $params['#'] = ElFinder::genPathHash($this->startPath);

                $cur_manager_options['url'] = ElFinder::getManagerUrl($this->controller, $params);
                $cur_manager_options['id'] = $this->options['id'] . '-' . $key;

                if (!empty($this->multiple))
                    $this->getView()->registerJs(
                        "mihaildev.elFinder.register(" . Json::encode($cur_options['id']) . ", 
                        function(files, id){ 
                            var _f = []; 
                            for (var i in files) { 
                                _f.push(files[i].url); 
                            } 
                            \$('#' + id).val(_f.join(', ')).trigger('change', [files, id]); 
                            return true;
                        }); 
                    ");
                else {
                    $this->getView()->registerJs(
                        "mihaildev.elFinder.register(" . Json::encode($cur_options['id']) . ", 
                        function(file, id){ 
                            \$('#' + id).val(file.url).trigger('change', [file, id]); 
                            \$('#' + id + '-image').attr('src', file.url); 
                            return true;
                        }); 
                    ");
                }
                $key++;
            }
        }

        $html .= Sortable::widget([
            'items' => $items,
            'options' => [
                'id' => $this->id,
                'tag' => 'div',
                'class' => 'col-sm-12',
            ],
            'itemOptions' => [
                'tag' => 'div',
                'class' => 'col-lg-2 col-md-3 col-sm-4 col-xs-6 image-column',
            ],
            'clientOptions' => [
                'snap' => true,
                'zIndex' => 100,
                'refreshPositions' => true,
                'snapMode' => 'inner',
//                    'containment' => 'parent',
                'distance' => 10,
                'opacity' => 0.8,
                'forcePlaceholderSize' => true,
                'handle' => '.picture',
                'placeholder' =>'sortable-placeholder col-lg-2 col-md-3 col-sm-4 col-xs-6',
                'scroll' => false,
                'cursor' => 'grabbing',
                'tolerance' => 'pointer',
            ],
        ]).
            '</div>'.
        '</div>';

        echo $html;

        AssetsCallBack::register($this->getView());

        $this->getView()->registerJs(
            "var images_container = $('#".$this->id."'), new_added = [];
                var html = '', images_count = images_container.find('.image-column').length + 1;

                images_container.delegate('.btn-browse-file', 'click', function(){
                    var cur_id = $(this).data('id');
                    mihaildev.elFinder.openManager({
                        \"url\": \"/admin/elfinder/manager?filter=image&callback=\"+cur_id+\"&lang=ru&path=image\",
                        \"width\": \"auto\",
                        \"height\": \"auto\",
                        \"id\": \"+cur_id+\"
                    });
                });
                
                images_container.delegate('.btn-delete-file', 'click', function() {
                    var item_img = $(this).closest('.image-column');
                    item_img.fadeOut( 500, \"linear\", function(){ ;
                        item_img.remove();
                        $('#".$this->id."').sortable('refresh');
                        $(window).trigger('resize');
                    });
                    images_count = images_container.find('.image-column').length + 1;
                });

                images_container.sortable({
                    update: function( event, ui ) {
                        var i2 = 1;
                        images_container.find('.image-column').each(function (index, value){
                            $(this).find('.sort-order-input').val(i2);
                            i2++;
                        });
                    }
                });

                $('#".$this->addButtonOptions['id']."').on('click', function() {
                    images_count = images_container.find('.image-column').length + 1;
                    html = '\
                    <div class=\"col-lg-2 col-md-3 col-sm-4 col-xs-6 image-column\">\
                        <div class=\"picture-div\">\
                            <div class=\"picture\">\
                                <img id=\"".$this->options['id']."-'+images_count+'-image\" class=\"img-responsive\" src=\"/backend/components/placeholder.png\" alt=\"\">\
                            </div>\
                            <input type=\"hidden\" id=\"".$this->options['id']."-'+images_count+'\" class=\"image-path-input\" name=\"".($this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name)."['+images_count+'][".$this->filePathName."]\" value=\"\">\
                            <input type=\"hidden\" id=\"".$this->options['id']."-'+images_count+'_".$this->sortOrderName."\" class=\"sort-order-input\" name=\"".($this->hasModel() ? Html::getInputName($this->model, $this->attribute): $this->name)."['+images_count+'][".$this->sortOrderName."]\" value=\"\">\
                            <div class=\"btn-group\">\
                                <button type=\"button\" id=\"".$this->options['id']."-'+images_count+'_button\" data-id=\"".$this->options['id']."-'+images_count+'\" class=\"btn btn-primary btn-browse-file\" title=\"" . $this->buttonName . "\"><span class=\"glyphicon glyphicon-pencil\"></span></button>\
                                <button type=\"button\" id=\"".$this->options['id']."-'+images_count+'_delete\" class=\"btn btn-danger btn-delete-file\"title=\"" . $this->deleteButtonName . "\"><span class=\"glyphicon glyphicon-trash\"></span></button>\
                            </div>\
                        </div>\
                    </div>';
                    
                    images_container.append(html);
                    images_container.sortable('refresh');
                    $(window).trigger('resize');
                    var i2 = 0;
                    images_container.find('.image-column').each(function (index, value){
                        $(this).find('.sort-order-input').val(i2);
                        i2++;
                    });
                    
                    mihaildev.elFinder.register('".$this->options['id']."-'+images_count, function(file, id){ 
                        $('#' + id).val(file.url).trigger('change', [file, id]); 
                        $('#' + id + '-image').attr('src', file.url);
                        return true;
                    });
                    
                    
                    $('#".$this->options['id']."-'+images_count+'_button').trigger('click'); 
                });
        ");
    }
}