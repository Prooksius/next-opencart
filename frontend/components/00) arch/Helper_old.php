<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 19.06.2019
 * Time: 18:33
 */

namespace frontend\components;

use Yii;

class Helper
{
    public static function utf8_substr($string, $offset, $length = null) {
        if ($length === null) {
            return mb_substr($string, $offset, utf8_strlen($string));
        } else {
            return mb_substr($string, $offset, $length);
        }
    }

    public static function plural_type($n) {
        return ($n%10==1 && $n%100!=11 ? 0 : ($n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2));
    }

    public static function plural_name($n) {
        return ($n%10==1 && $n%100!=11 ? 0 : 1);
    }

    public static function uniqueCombination($in, $minLength = 1, $max = 2000) {
        $count = count($in);
        $members = pow(2, $count);
        $return = array();
        for($i = 0; $i < $members; $i ++) {
            $b = sprintf("%0" . $count . "b", $i);
            $out = array();
            for($j = 0; $j < $count; $j ++) {
                $b{$j} == '1' and $out[] = $in[$j];
            }

            count($out) >= $minLength && count($out) <= $max and $return[] = $out;
        }
        return $return;
    }

    public static function rus_date() {
        // Перевод
        $translate = array(
            "am" => "дп",
            "pm" => "пп",
            "AM" => "ДП",
            "PM" => "ПП",
            "Monday" => "Понедельник",
            "Mon" => "Пн",
            "Tuesday" => "Вторник",
            "Tue" => "Вт",
            "Wednesday" => "Среда",
            "Wed" => "Ср",
            "Thursday" => "Четверг",
            "Thu" => "Чт",
            "Friday" => "Пятница",
            "Fri" => "Пт",
            "Saturday" => "Суббота",
            "Sat" => "Сб",
            "Sunday" => "Воскресенье",
            "Sun" => "Вс",
            "January" => "января",
            "Jan" => "янв",
            "February" => "февраля",
            "Feb" => "фев",
            "March" => "марта",
            "Mar" => "мар",
            "April" => "апреля",
            "Apr" => "апр",
            "May" => "мая",
            "May" => "мая",
            "June" => "июня",
            "Jun" => "июн",
            "July" => "июля",
            "Jul" => "июл",
            "August" => "августа",
            "Aug" => "авг",
            "September" => "сентября",
            "Sep" => "сен",
            "October" => "октября",
            "Oct" => "окт",
            "November" => "ноября",
            "Nov" => "ноя",
            "December" => "декабря",
            "Dec" => "дек",
            "st" => "ое",
            "nd" => "ое",
            "rd" => "е",
            "th" => "ое"
        );
        if (func_num_args() > 1) {  // если передали дату, то переводим ее
            $timestamp = func_get_arg(1);
            return strtr(date(func_get_arg(0), $timestamp), $translate);
        } else {  // иначе текущую дату
            return strtr(date(func_get_arg(0)), $translate);
        }
    }

    public static function send_b24_lead($b24_access, $title, $mode, $data = [])
    {
        // формируем URL в переменной $queryUrl
        //$queryUrl = 'https://stock.bitrix24.ru/rest/'.$b24_access['user_id'].'/'.$b24_access['webhook'].'/crm.lead.add.json';
        $queryUrl = 'https://stock.bitrix24.ru/rest/1694/bw5s5euytbypejx1/crm.lead.add.json';

        // формируем параметры для создания лида в переменной $queryData
        $queryData = http_build_query(array(
            'fields' => array(
                'TITLE' => $title,
                'NAME' => $data['name'],
                'SOURCE_ID' => ($mode == 'request' ? '80' : '79'),
                'SOURCE_DESCRIPTION' => $title,
                'OPPORTUNITY' => $data['amount'],
                'CURRENCY_ID' => 'RUB',
                'UF_CRM_1588270065' => $data['promocode'],
                'UF_CRM_1588270107' => $data['botname'],
                'COMMENTS' => (isset($data['comment']) ? $data['comment'] : ''),
                'EMAIL' => Array(
                    "n0" => Array(
                        "VALUE" => $data['email'],
                        "VALUE_TYPE" => "WORK",
                    ),
                ),
                'PHONE' => Array(
                    "n0" => Array(
                        "VALUE" => $data['phone'],
                        "VALUE_TYPE" => "WORK",
                    ),
                ),
				'UTM_CAMPAIGN' => Yii::$app -> session -> get('utm_campaign', ''),
				'UTM_CONTENT' => Yii::$app -> session -> get('utm_content', ''),
				'UTM_MEDIUM' => Yii::$app -> session -> get('utm_medium', ''),
				'UTM_SOURCE' => Yii::$app -> session -> get('utm_source', ''),
				'UTM_TERM' => Yii::$app -> session -> get('utm_term', ''),
            ),
            'params' => array("REGISTER_SONET_EVENT" => "Y")
        ));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, 1);

        file_put_contents(Yii::$app->basePath . '/1111.txt', json_encode($result));

    }
}