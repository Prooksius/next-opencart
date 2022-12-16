<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 27.04.2020
 * Time: 14:38
 */

use yii\helpers\Html;

$this->title = 'Профиль | Личный кабинет | ' . $seo['meta_title'];

?>

<section class="privateOffice<?= ($is_partner ? ' privateOffice1' : '') ?>">
    <div class="container">
        <?= $this->render('account-menu'); ?>
        <div class="profile">
            <div class="profile-wrap">
                <div class="profile-img">
                    <?= $this->render('photoupload',   ['model' => $photo_upload]); ?>
                </div>
                <div class="profile-input-container">
                    <table>
                        <tr>
                            <td>Логин: </td>
                            <td><b><?= $customer->username ?></b></td>
                        </tr>
                        <tr>
                            <td>Имя: </td>
                            <td><b><?= $customer->first_name ?></b></td>
                        </tr>
                        <tr>
                            <td>Email: </td>
                            <td><b><?= $customer->email ?></b></td>
                        </tr>
                        <tr>
                            <td>Телефон: </td>
                            <td><b><?= $customer->phone ?></b></td>
                        </tr>
                        <tr>
                            <td>Телеграм: </td>
                            <td><b><?= $customer->telegram ?></b></td>
                        </tr>
                    </table>
                    <?= Html::button('Редактировать профиль', ['class' => 'btn2 form-popup', 'data-target' => '/account/profile-edit-popup'])?>
                </div>
            </div>
        </div>
    </div>
</section>