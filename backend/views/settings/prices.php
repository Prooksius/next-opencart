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

<?= MyHtml::formGroup('prices', 'meta_title', 'Title страницы', $prices['meta_title'])?>
<?= MyHtml::formGroup('prices', 'meta_desc', 'Description страницы', $prices['meta_desc'])?>
