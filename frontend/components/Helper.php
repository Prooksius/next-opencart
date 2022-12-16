<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 19.06.2019
 * Time: 18:33
 */

namespace frontend\components;

use Yii;
use yii\helpers\ArrayHelper;

class Helper
{

    public static function token($length = 32) {
    // Create random token
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $max = strlen($string) - 1;

        $token = '';

        for ($i = 0; $i < $length; $i++) {
        $token .= $string[mt_rand(0, $max)];
        }

        return $token;
    }

    public static function numToken($length = 10) {
    // Create random token
        $string = '0123456789';

        $max = strlen($string) - 1;

        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $string[mt_rand(0, $max)];
        }

        return $token;
    }

    public static function cleanData($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[self::cleanData($key)] = self::cleanData($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }

    public static function recaptchaCheck()
    {
        $action = Yii::$app->request->post('action', '');
        $token = Yii::$app->request->post('token', '');
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $params = [
            'secret' => Yii::$app->params['recaptchaSecret'],
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'],
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        if (!empty($response)) $decoded_response = json_decode($response);

        if ($decoded_response && $decoded_response->success && $decoded_response->action == $action && $decoded_response->score > 0) {
            return true;
        }
//		var_dump($decoded_response);
        return false;
    }

    public static function getErrorFromModel($model) {
        $error = '';
        foreach ($model->getErrors() as $key => $value) {
            $error = YII::t('app', $value[0]);
            break;
        }
        return $error;
    }

    public static function getArrayFullFromText($text) {
      $text_lines = explode(PHP_EOL, $text);
      $array = [];
      foreach ($text_lines as $key => $line) {
        $array[] = [
          'value' => $key,
          'text' => $line
        ];
      }
      return $array;
    }

    public static function getArrayFromText($text) {
      return explode(PHP_EOL, $text);
    }

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

    public static function eng_months($month) {
        $months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return $months[$month];
    }

    public static function rus_months($month) {
        $months = ['', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
        return $months[$month];
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
        
        file_put_contents(Yii :: $app -> basePath . '/data.txt', json_encode($data));
        
        // формируем URL в переменной $queryUrl
        //$queryUrl = 'https://stock.bitrix24.ru/rest/'.$b24_access['user_id'].'/'.$b24_access['webhook'].'/crm.lead.add.json';
        $query_url = 'https://stock.bitrix24.ru/rest/1694/bw5s5euytbypejx1/crm.lead.add.json';
        //$query_url = 'https://stock.bitrix24.ru/rest/1694/bw5s5euytbypejx1/crm.deal.add.json';

        //$rest_api_url = 'https://stock.bitrix24.ru/rest/1694/bw5s5euytbypejx1/';
        $rest_api_url = 'https://stock.bitrix24.ru/rest/1/ajyz159ll3xf6uzr/';

        $SOURCE_ID_ARRAY['to-become-copy-traider'] = '94';
        $CATEGORY_ID_ARRAY['to-become-copy-traider'] = '20';
        $FORM_NAME_ARRAY['to-become-copy-traider'] = 'Стать Copy-трейдером';
        
        $SOURCE_ID_ARRAY['consult-request'] = '92';
        $CATEGORY_ID_ARRAY['consult-request'] = '20';
        $FORM_NAME_ARRAY['consult-request'] = 'Стать Copy-трейдером';
        

        // Deal creation //
        
        $SOURCE_ID = $SOURCE_ID_ARRAY[$data['form_name']];
        $CATEGORY_ID = $CATEGORY_ID_ARRAY[$data['form_name']];
        $FORM_NAME = $FORM_NAME_ARRAY[$data['form_name']];
        
        $CUSTOM_NAME_B24_ID = 'UF_CRM_1623227525314';
        $CUSTOM_SECOND_NAME_B24_ID = 'UF_CRM_60C0710E497FC';
        $CUSTOM_PHONE_B24_ID = 'UF_CRM_60C0710E55A50';
        $CUSTOM_EMAIL_B24_ID = 'UF_CRM_60C0710E5D192';
        

        // Find contact //

        // $contact_find_url = $rest_api_url.'crm.contact.list.json';

        // if ($data['phone'] != ''){
            // $post_data['filter']['PHONE'] = $data['phone'];
            // $post_data['select'] = ['ID', 'NAME', 'LAST_NAME', 'EMAIL', 'ADDRESS', 'WEB', 'PHONE'];
            // $post_data = http_build_query($post_data);
        // }
        
        // //file_put_contents(Yii :: $app -> basePath . '/lead_result.txt', json_encode($result));

        // $curl = curl_init();
            // CURL_SETOPT_ARRAY($curl, array(
            // CURLOPT_SSL_VERIFYPEER => 0,
            // CURLOPT_POST => 1,
            // CURLOPT_HEADER => 0,
            // CURLOPT_RETURNTRANSFER => 1,
            // CURLOPT_URL => $contact_find_url,
            // CURLOPT_POSTFIELDS => $post_data,
        // ));

        // $result_contact_find = json_decode(curl_exec($curl), true);
        // $result_contact_find = $result_contact_find['result']['0'];
        // curl_close($curl);
       
        
        // file_put_contents(Yii :: $app -> basePath . '/contact_id.txt', json_encode($contact_id));
        
        // Deal create //
        
        // Запрос консультации о наиболее эффективном способе вложения. с сайта Сакура - Оставить заявку на подключение
        // Запрос 14-дневного тестового доступа с сайта Copy-Trade - Стать копитрейдером

        $deal_data['fields']['TITLE'] = $title;
        $deal_data['fields']['UF_CRM_1588270065'] = $data['promocode'];
        $deal_data['fields']['UF_CRM_1588270107'] = $data['botname'];
        $deal_data['fields']['SOURCE_ID'] = $SOURCE_ID;
        $deal_data['fields']['CATEGORY_ID'] = $CATEGORY_ID;
        $deal_data['fields']['COMMENTS'] = (isset($data['comment']) ? $data['comment'] : '');
        
        $deal_data['fields'][$CUSTOM_NAME_B24_ID] = $data['name'];
        $deal_data['fields'][$CUSTOM_SECOND_NAME_B24_ID] = '';
        $deal_data['fields'][$CUSTOM_PHONE_B24_ID] = [$data['phone']];
        $deal_data['fields'][$CUSTOM_EMAIL_B24_ID] = [$data['email']];
        
		$deal_data['fields']['UTM_CAMPAIGN'] = Yii :: $app -> session -> get('utm_campaign', '');
		$deal_data['fields']['UTM_CONTENT'] = Yii :: $app -> session -> get('utm_content', '');
		$deal_data['fields']['UTM_MEDIUM'] = Yii :: $app -> session -> get('utm_medium', '');
		$deal_data['fields']['UTM_SOURCE'] = Yii :: $app -> session -> get('utm_source', '');
		$deal_data['fields']['UTM_TERM'] = Yii :: $app -> session -> get('utm_term', '');
        
        //$deal_data['fields']['CONTACT_IDS'] = [$contact_id];
        
        $deal_data = http_build_query($deal_data);
        $deal_url = $rest_api_url.'crm.deal.add.json';
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $deal_url,
            CURLOPT_POSTFIELDS => $deal_data,
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, 1);
        
        file_put_contents(Yii :: $app -> basePath . '/deal_info.txt', json_encode($result));

        ////////////////////////////////////////////////////////////////////////////////////////

    }
}