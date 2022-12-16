<?php

namespace common\components;

use Yii;
use yii\helpers\FileHelper;

class Logger
{

  const DEFAULT_LOG = '/logger.log';
  const PAYMENT_LOG = '/payment_system.log';

  private static function _log($log_type, $title, $message = '') {
    file_put_contents(
      FileHelper::normalizePath(YII::getAlias('@root') . $log_type), 
      date('d.m.Y H:i:s') . ' - ' . $title . ' - ' . $message . "\r\n", 
      FILE_APPEND
    );
  }

  public static function log($title, $message = '') {
    self::_log(self::DEFAULT_LOG, $title, $message);
  }

  public static function paymentLog($title, $message = '') {
    self::_log(self::PAYMENT_LOG, $title, $message);
  }

}