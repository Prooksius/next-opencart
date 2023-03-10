<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 19.06.2019
 * Time: 18:33
 */

namespace app\components;

use Yii;

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

    public static function isColorBright($color)
    {
      $hex = ltrim($color, '#');

      //break up the color in its RGB components
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));

      $contrast = sqrt(
          $r * $r * .241 +
          $g * $g * .691 +
          $b * $b * .068
      );

      if($contrast > 130){
          return true;
      }else{
          return false;
      }
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

    public static function getErrorFromModel($model) {
        $error = '';
        foreach ($model->getErrors() as $key => $value) {
            $error = YII::t('app', $value[0]);
            break;
        }
        return $error;
    }

    public static function getArrayFromText($text) {
      $text_lines = explode(PHP_EOL, $text);
      $array = [];
      foreach ($text_lines as $line) {
        $array[$line] = $line;
      }
      return $array;
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

    public static function rus_date() {
        // ??????????????
        $translate = array(
            "am" => "????",
            "pm" => "????",
            "AM" => "????",
            "PM" => "????",
            "Monday" => "??????????????????????",
            "Mon" => "????",
            "Tuesday" => "??????????????",
            "Tue" => "????",
            "Wednesday" => "??????????",
            "Wed" => "????",
            "Thursday" => "??????????????",
            "Thu" => "????",
            "Friday" => "??????????????",
            "Fri" => "????",
            "Saturday" => "??????????????",
            "Sat" => "????",
            "Sunday" => "??????????????????????",
            "Sun" => "????",
            "January" => "????????????",
            "Jan" => "??????",
            "February" => "??????????????",
            "Feb" => "??????",
            "March" => "??????????",
            "Mar" => "??????",
            "April" => "????????????",
            "Apr" => "??????",
            "May" => "??????",
            "May" => "??????",
            "June" => "????????",
            "Jun" => "??????",
            "July" => "????????",
            "Jul" => "??????",
            "August" => "??????????????",
            "Aug" => "??????",
            "September" => "????????????????",
            "Sep" => "??????",
            "October" => "??????????????",
            "Oct" => "??????",
            "November" => "????????????",
            "Nov" => "??????",
            "December" => "??????????????",
            "Dec" => "??????",
            "st" => "????",
            "nd" => "????",
            "rd" => "??",
            "th" => "????"
        );
        if (func_num_args() > 1) {  // ???????? ???????????????? ????????, ???? ?????????????????? ????
            $timestamp = func_get_arg(1);
            return strtr(date(func_get_arg(0), $timestamp), $translate);
        } else {  // ?????????? ?????????????? ????????
            return strtr(date(func_get_arg(0)), $translate);
        }
    }
}