<?php
namespace frontend\components;

use frontend\models\Sef;
use yii\web\UrlRuleInterface;
use yii\base\BaseObject;

class SefRule extends BaseObject implements UrlRuleInterface{

    public $connectionID = 'db';
    public $name;

    public function init(){
        if ($this->name === null) {
            $this->name = __CLASS__;
        }
    }

    //Формирует ссылки в заданном виде (часть формируется в коде, остальное берется из БД)
    public function createUrl($manager, $route, $params){
        //debug($route);
        //Определяем контроллеры, у которых к страницам нужно добавлять .html
        $controller = explode('/',$route)[0]; //Получаем контроллер
        $html = '';

        //Если передаются параметры (напрмиер ?id=3&page=2) сохраняем в $link по-очереди
        $link ='';
        $page ='';
        if(count($params)){
            $link = "?";
            $page = false;
            foreach ($params as $key => $value){
                if($key == 'page'){
                    $page = $value;
                    continue;
                }
                $link .= "$key=$value&";
            }
            $link = substr($link, 0, -1); //удаляем последний символ (&)
        }
        //Из БД получаем строку со ссылкой на которую нужно будет поменять
        $sef = Sef::find()->where(['link' => $route.$link])->one();

        if ($sef){
            //Если есть - добавляем пагинацию в конец (?page=2)
            if ($page) return $sef->link_sef."$html?page=$page";
            else return $sef->link_sef.$html;
        }
        return false;
    }

    //Разбирает входящий URL запрос, преобразует ссылки произвольного вида (из БД поле link_sef) в нужный для Yii2
    public function parseRequest($manager, $request){

        //Получаем URL
        $pathInfo = $request->getPathInfo();

        //получаем из БД данные по строке содержащей заданный алиас
        $sef  = Sef::find()->where(['link_sef' => $pathInfo])->one();

        if($sef){
            //Разбивает строку типа post/view?id=5 на массив по разделителю
            $link_data = explode('?',$sef->link);
            //берем только первую часть без параметров (контроллер/действие)
            $route = $link_data[0];
            $params = array();
            //если есть параметры - вставляем их
            if(isset($link_data[1])){
                $temp = explode('&',$link_data[1]);
                foreach($temp as $t){
                    $t = explode('=', $t);
                    $params[$t[0]] = $t[1];
                }
            }
            //$route - контроллер/действие
            //$params - параметры
            return [$route, $params];
        }
        return false;
    }
}