<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 05.06.2019
 * Time: 11:27
 */

use yii\helpers\Html;
use backend\components\MyHtml;

?>

<?= MyHtml::formGroup('brief', 'meta_title', 'Title страницы', $brief['meta_title'])?>
<?= MyHtml::formGroup('brief', 'meta_desc', 'Description страницы', $brief['meta_desc'])?>
