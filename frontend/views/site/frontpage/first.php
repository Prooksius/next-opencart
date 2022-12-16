<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 01.05.2020
 * Time: 22:14
 */

use frontend\components\Helper;
use yii\helpers\Html;
?>

<div class="wrapper wrapper-main-page" style="background-image:url('img/bg.jpg');">
	<div class="bg-layer">
		<div class="line">
			<div class="animated-chunk"></div>
		</div>
	</div>
	<div class="container">
		<div class="top-panel d-flex">
			<a href="#" class="logo">
				<img src="img/logo_new.png" alt="CT">
			</a>
			<div class="buttons d-flex">
				<?php if (Yii::$app->user->isGuest) { ?>
				<?= Html::button('Вход', ['class' => 'login-btn d-flex form-popup', 'data-target' => '/site/loginpopup', 'data-goal' => 'login', 'id' => 'login'])?>
				<?= Html::button('Регистрация', ['class' => 'register-btn d-flex form-popup', 'data-target' => '/site/signuppopup', 'data-goal' => 'registration', 'id' => 'registration'])?>
				<?php } else { ?>
				<?= Html::a('Выход', ['/site/logout'], ['class' => 'login-btn d-flex'])?>
				<?= Html::a('Аккаунт', ['/account'], ['class' => 'register-btn d-flex'])?>
				<?php } ?>
			</div>
		</div>
		<img src="img/full-logo.png" alt="Copy trade" class="name-img">
		<p class="offer-text">копируй лучших трейдеров, <span>получай доход</span> на своём брокерском счете</p>
		<div class="main-buttons d-flex">
			<?= Html::button('Стать Copy-трейдером', ['class' => 'target-btn d-flex form-popup', 'data-target' => '/site/test-request-popup', 'data-goal' => 'buttonClickTry'])?>
			<?= Html::button('Оставить заявку на подключение', ['class' => 'callback-btn d-flex form-popup', 'data-target' => '/site/income-request-popup', 'data-goal' => 'buttonClickTry'])?>
		</div>
		<p class="contact-text">
			Свяжитесь с нами любым удобным способом
		</p>
		<div class="contact-buttons d-flex">
			<a href="<?= $social['tg'] ?>" class="contact-btn d-flex telegram">
				<img src="img/telegram.png" alt="Tg" class="icon">
				<span>Написать в Telegram</span>
			</a>
			<a href="<?= $social['wa'] ?>" class="contact-btn d-flex whatsapp">
				<img src="img/whatsapp.png" alt="WA" class="icon">
				<span>Написать в Онлайн чат</span>
			</a>
		</div>
	</div>
</div>


    <?php
    
        $CUSTOM_NAME_B24_ID = 'UF_CRM_1623227525314';
        $CUSTOM_SECOND_NAME_B24_ID = 'UF_CRM_60C0710E497FC';
        $CUSTOM_PHONE_B24_ID = 'UF_CRM_60C0710E55A50';
        $CUSTOM_EMAIL_B24_ID = 'UF_CRM_60C0710E5D192';
        
        $CUSTOM_LOGIN_B24_ID = 'UF_CRM_1624440652';
    
        require_once($_SERVER['DOCUMENT_ROOT'].'/frontend/b24/classes/request_class.php');
        require_once($_SERVER['DOCUMENT_ROOT'].'/frontend/b24/classes/crest.php');
    
        $post_data = $_POST;
    
        //if ($post_data != null) {
            
            if ($post_data['login'] != '') {

                $deal_data['fields']['TITLE'] = 'Регистрация';
                $deal_data['fields']['UF_CRM_1588270065'] = '';
                $deal_data['fields']['UF_CRM_1588270107'] = '';
                $deal_data['fields']['SOURCE_ID'] = '93'; 
                $deal_data['fields']['CATEGORY_ID'] = '20';
                //$deal_data['fields']['COMMENTS'] = (isset($data['comment']) ? $data['comment'] : '');
                
                $deal_data['fields'][$CUSTOM_NAME_B24_ID] = $post_data['fio'];
                $deal_data['fields'][$CUSTOM_SECOND_NAME_B24_ID] = '';
                $deal_data['fields'][$CUSTOM_PHONE_B24_ID] = [$post_data['phone']];
                $deal_data['fields'][$CUSTOM_EMAIL_B24_ID] = [$post_data['email']];
                $deal_data['fields'][$CUSTOM_LOGIN_B24_ID] = $post_data['login'];
                $deal_data['fields']['STAGE_ID'] = 'C20:PREPARATION';
                
                $deal_data['fields']['UTM_CAMPAIGN'] = Yii :: $app -> session -> get('utm_campaign', '');
                $deal_data['fields']['UTM_CONTENT'] = Yii :: $app -> session -> get('utm_content', '');
                $deal_data['fields']['UTM_MEDIUM'] = Yii :: $app -> session -> get('utm_medium', '');
                $deal_data['fields']['UTM_SOURCE'] = Yii :: $app -> session -> get('utm_source', '');
                $deal_data['fields']['UTM_TERM'] = Yii :: $app -> session -> get('utm_term', '');
                
                $deal_create = CRest :: call('crm.deal.add', $deal_data);
                
                help_class :: write_data_to_file($_SERVER['DOCUMENT_ROOT'].'/frontend/deal_create.txt', $deal_create);
                help_class :: write_data_to_file($_SERVER['DOCUMENT_ROOT'].'/frontend/data.txt', $post_data);
                
            }    
            
        //}
  
        //$company_find = CRest :: call('crm.company.list', ['filter' => [COMPANY_INN_NAME => $company_inn]]);

        //print_r($_SERVER['DOCUMENT_ROOT']);
        
        class help_class{

            public static function write_data_to_file($file_path, $data_array) {

                $data_array = serialize($data_array);
                file_put_contents($file_path, $data_array);
                $fp = fopen ($file_path, "w"); // Открытие файла на чтение
                fwrite($fp, $data_array);
                fclose($fp);
                return $data_array;
            
            } 
            
        }    
        
    ?> 
