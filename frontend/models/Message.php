<?php

namespace frontend\models;

use common\models\Message as ModelsMessage;
use Yii;

/**
 * This is the model class for table "external_wallet".
 */
class Message extends ModelsMessage
{
  public $title;
  public $description;

  public static $typeTranslation = [
    self::TYPE_PROFILE => 'Profile',
    self::TYPE_PARTNER => 'MenuPartnership',
    self::TYPE_BONUS => 'Bonus',
    self::TYPE_TRADER => 'TraderMessage',
  ];

  public static $icons = [
    self::ICON_USER => 'icons/partnership.svg',
    self::ICON_MAIL => 'icons/mail.svg',
    self::ICON_BONUS => 'icons/gift.svg',
    self::ICON_INFO => 'icons/alert2.svg',
  ];

  public static function getUnreadMessages($customer_id)
  {
    $messages = [];
    $recents = self::find()
      ->alias('m')
      ->select([
        'm.*',
        'md.name AS title',
        'md.description AS description',
      ])
      ->leftJoin('message_desc md', 'md.message_id = m.id AND md.language_id = "' . Yii::$app->language . '"')
      ->where(['customer_id' => $customer_id, 'viewed' => 0])
      ->orderBy(['created_at' => SORT_DESC])
      ->all();
    
    foreach ($recents as $message) {
      $messages[] = [
        'id' => $message->id,
        'type' => Translation::getTranslation(self::$typeTranslation[(int)$message->type]),
        'icon' => self::$icons[(int)$message->icon],
        'title' => $message->title,
        'description' => $message->description,
        'system' => false,
        'hidden' => false, 
      ];
    }

    return $messages;
  }
}
