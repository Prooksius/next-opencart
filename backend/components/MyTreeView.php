<?php

namespace backend\components;

/**
 * CTreeView class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\base\Exception;
use yii\helpers\Url;

/**
 * CTreeView displays a tree view of hierarchical data.
 *
 * It encapsulates the excellent tree view plugin for jQuery
 * ({@link http://bassistance.de/jquery-plugins/jquery-plugin-treeview/}).
 *
 * To use CTreeView, simply sets {@link data} to the data that you want
 * to present and you are there.
 *
 * CTreeView also supports dynamic data loading via AJAX. To do so, set
 * {@link url} to be the URL that can serve the tree view data upon request.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system.web.widgets
 * @since 1.0
 */
class MyTreeView extends Widget
{

    const TEMPLATE = '<div class="tree-view-wrapper">
        <div class="row tree-header">
            <div class="col-sm-12">
                <div class="tree-heading-container">{header}</div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                {tree}
            </div>
        </div>
    </div>';

    public $controller = '';

    /**
     * @var array the data that can be used to generate the tree view content.
     * Each array element corresponds to a tree view node with the following structure:
     * <ul>
     * <li>text: string, required, the HTML text associated with this node.</li>
     * <li>expanded: boolean, optional, whether the tree view node is expanded.</li>
     * <li>id: string, optional, the ID identifying the node. This is used
     *   in dynamic loading of tree view (see {@link url}).</li>
     * <li>hasChildren: boolean, optional, defaults to false, whether clicking on this
     *   node should trigger dynamic loading of more tree view nodes from server.
     *   The {@link url} property must be set in order to make this effective.</li>
     * <li>children: array, optional, child nodes of this node.</li>
     * <li>htmlOptions: array, additional HTML attributes (see {@link CHtml::tag}).
     *   This option has been available since version 1.1.7.</li>
     * </ul>
     * Note, anything enclosed between the beginWidget and endWidget calls will
     * also be treated as tree view content, which appends to the content generated
     * from this data.
     */
    public $data;
    /**
     * @var string|array the URL to which the treeview can be dynamically loaded (in AJAX).
     * See {@link CHtml::normalizeUrl} for possible URL formats.
     * Setting this property will enable the dynamic treeview loading.
     * When the page is displayed, the browser will request this URL with a GET parameter
     * named 'root' whose value is 'source'. The server script should then generate the
     * needed tree view data corresponding to the root of the tree (see {@link saveDataAsJson}.)
     * When a node has a CSS class 'hasChildren', then expanding this node will also
     * cause a dynamic loading of its child nodes. In this case, the value of the 'root' GET parameter
     * is the 'id' property of the node.
     */
    public $url;
    /**
     * Widget header
     *
     * @var string
     */
    public $header = 'Select from tree';
    /**
     * @var string|integer animation speed. This can be one of the three predefined speeds
     * ("slow", "normal", or "fast") or the number of milliseconds to run the animation (e.g. 1000).
     * If not set, no animation is used.
     */
    public $animated;
    /**
     * @var boolean whether the tree should start with all branches collapsed. Defaults to false.
     */
    public $collapsed;
    /**
     * @var string container for a tree-control, allowing the user to expand, collapse and toggle all branches with one click.
     * In the container, clicking on the first hyperlink will collapse the tree;
     * the second hyperlink will expand the tree; while the third hyperlink will toggle the tree.
     * The property should be a valid jQuery selector (e.g. '#treecontrol' where 'treecontrol' is
     * the ID of the 'div' element containing the hyperlinks.)
     */
    public $control;
    /**
     * @var boolean set to allow only one branch on one level to be open (closing siblings which opening).
     * Defaults to false.
     */
    public $unique;
    /**
     * @var string Callback when toggling a branch. Arguments: "this" refers to the UL that was shown or hidden
     */
    public $toggle;
    /**
     * @var array additional options that can be passed to the constructor of the treeview js object.
     */
    public $options=array();
    /**
     * Main container html options
     *
     * @var array
     */
    public $containerOptions = [];
    /**
     * Template for render widget
     *
     * @var string
     */
    public $template = self::TEMPLATE;
    /**
     * @var array additional HTML attributes that will be rendered in the UL tag.
     * The default tree view CSS has defined the following CSS classes which can be enabled
     * by specifying the 'class' option here:
     * <ul>
     * <li>treeview-black</li>
     * <li>treeview-gray</li>
     * <li>treeview-red</li>
     * <li>treeview-famfamfam</li>
     * <li>filetree</li>
     * </ul>
     */
    public $htmlOptions;
    /**
     * Initializes the widget.
     * This method registers all needed client scripts and renders
     * the tree view content.
     */
    public function init()
    {
        if(!isset($this->options['id']))
            $this->options['id'] = $this->getId();
        if(!isset($this->options['class']) || !$this->options['class']) {
            $this->options['class'] = 'tree-container';
        } else {
            $this->options['class'] .= ' tree-container';
        }
        $tree = '<ul class="dd-list">' . $this->saveDataAsHtml($this->data) . '</ul>';

        if (strpos($this->template, '{tree}') === false) {
            throw new Exception('{tree} not found in widget template');
        }
        $parts = [
            '{tree}' => Html::tag('div', $tree, ['class' => 'tree-div']),
            '{header}' => $this->header,
        ];

        echo Html::tag('div', strtr($this->template, $parts), $this->options);

    }
    /**
     * Ends running the widget.
     */
    public function run()
    {
        $this->getView()->registerJsFile('/backend/components/jquery.nestable.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
        $this->getView()->registerJs("
            var unique = " . ($this->unique ? 'true' : 'false') . ";

            $('#".$this->options['id']."').nestable();

            $('#".$this->options['id']." .buttonh').on('click', function() {
                var cur_li = $(this).closest('li');
                if (cur_li.hasClass('dd-expanded')) {
                    cur_li.find('.hasChildren.dd-expanded')
                          .removeClass('dd-expanded')
                          .addClass('dd-collapsed');
                    cur_li.find('.children')
                          .slideUp();
                } else {
                    if (unique) {
                        $('#".$this->options['id']." .hasChildren.dd-expanded').each(function (index, value){
                            if (!$(this).has(cur_li).length) {
                                $(this).removeClass('dd-expanded')
                                       .addClass('dd-collapsed')
                                       .find('> .children')
                                       .slideUp();
                            }
                        });
                    }
                }
            });
        ");
    }
    /**
     * @return array the javascript options
     */
    protected function getClientOptions()
    {
        $options=$this->options;
        foreach(array('url','animated','collapsed','control','unique','toggle') as $name)
        {
            if($this->$name!==null)
                $options[$name]=$this->$name;
        }
        return $options;
    }
    /**
     * Generates tree view nodes in HTML from the data array.
     * @param array $data the data for the tree view (see {@link data} for possible data structure).
     * @return string the generated HTML for the tree view
     */
    public function saveDataAsHtml($data)
    {
        $html = '';
        if(is_array($data)) {
            foreach($data as $node) {
                if(!isset($node['text']))
                    continue;
                $css = '';
                if(isset($node['expanded'])) {
                    if (isset($node['hasChildren']) && $node['hasChildren']) {
                        $css = $node['expanded'] ? 'dd-expanded' : 'dd-collapsed';
                    }
                } else {
                    $css = '';
                }
                $arrow = '';
                if(isset($node['hasChildren']) && $node['hasChildren']) {
                    if($css !== '')
                        $css .= ' ';
                    $css .= 'dd-item hasChildren';
                    if(isset($node['expanded']) && $node['expanded']) {
                        $arrow = '<span class="buttonh fa fa-angle-right" aria-hidden="true"></span>';
                    }
                } else {
                    if($css !== '')
                        $css .= ' ';
                    $css .= 'dd-item';
                }
                $options = array();
                if(isset($node['id']))
                    $options['id'] = $node['id'];
                if($css !== '') {
                    if(isset($options['class']))
                        $options['class'] .= ' ' . $css;
                    else
                        $options['class'] = $css;
                }
                $params2 = ['id' => (string) $node['key']];
                $params2[0] = $this->controller . '/update';

                $edit_link = Url::toRoute($params2);

                $params2 = ['id' => (string) $node['key']];
                $params2[0] = $this->controller . '/delete';

                $delete_link = Url::toRoute($params2);
                $edits = '<span class="edits">
                            <a href="'.$edit_link.'" title="Редактировать" aria-label="Редактировать" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                            <a href="'.$delete_link.'" title="Удалить" aria-label="Удалить" data-pjax="0"><span class="glyphicon glyphicon-trash"></span></a>
                          </span>';

                $html .= '<li' . Html::renderTagAttributes($options) . '>'.$arrow.'<span class="dd-handle node-name">' . $node['text'] . $edits . '</span>';
                if(!empty($node['children'])) {
                    $html .= '<ul class="dd-list children" '.(isset($node['expanded']) && $node['expanded'] ? '' : 'style="display:none;"').'>';
                    $html .= $this->saveDataAsHtml($node['children']);
                    $html .= '</ul>';
                }
                $html .= '</li>'."\n";
            }
        }
        return $html;
    }
    /**
     * Saves tree view data in JSON format.
     * This method is typically used in dynamic tree view loading
     * when the server code needs to send to the client the dynamic
     * tree view data.
     * @param array $data the data for the tree view (see {@link data} for possible data structure).
     * @return string the JSON representation of the data
     */
    public static function saveDataAsJson($data)
    {
        if(empty($data))
            return '[]';
        else
            return JSON::encode($data);
    }
}