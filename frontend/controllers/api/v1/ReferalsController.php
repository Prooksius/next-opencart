<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use frontend\models\Payback;
use Yii;
use frontend\components\ApiController;
use common\models\Customer;
use frontend\models\Payment;
use frontend\models\PaymentSearch;
use frontend\models\Qualification;
use himiklab\thumbnail\EasyThumbnailImage;

class ReferalsController extends ApiController
{

  private function _pictureThumbnails($items)
  {
    $referals = [];

    foreach ($items as $child) {

      $child_model = Customer::findOne($child['id']);

      $license_name = '-';
      $license_till = false;
      $active_license = $child_model->getActiveLicense();
      if ($active_license) {
        $license_till = $active_license->valid_till;
        $license_name = $active_license->botname;
      }

      $qualification = $child_model->getActiveQualification();
      $qualif_icon = '';
      $qualif_name = '-';
      if ($qualification) {
        $qualif_icon = $qualification->icon ? EasyThumbnailImage::thumbnailFileUrl(
            '@root' . $qualification->icon,
            35,
            50,
            EasyThumbnailImage::THUMBNAIL_INSET
          ) : '';
        $qualif_name = $qualification->name;
      }

      $referals[] = [
        'id' => $child['id'],
        'account_id' => $child['account_id'],
        'username' => $child['username'],
        'email' => $child['email'],
        'phone' => $child['phone'],
        'picture' => $child['picture'] ? EasyThumbnailImage::thumbnailFileUrl(
            '@root' . $child['picture'],
            100,
            100,
            EasyThumbnailImage::THUMBNAIL_INSET
        ) : '',
        'ref_link' => $child['ref_link'],
        'first_name' => $child['first_name'],
        'date' => date('d.m.Y', (int)$child['created_at']),
        'level' => (int)$child['level'],
        'boughts' => (float)$child['boughts'],
        'month_boughts' => (float)$child['month_boughts'],
        'license_name' => $license_name,
        'license_till' => $license_till ? date('d.m.Y', (int)$license_till) : '-',
        'qualification' => $qualif_name,
        'qualif_icon' => $qualif_icon,
      ];
    }
    return $referals;
  }

  private function _populateFields($items)
  {
    $referals = [];

    foreach ($items as $child) {

      $license_till = $child->getActiveLicenseTill();
      $qualification = $child->getActiveQualification();
      $qualif_icon = '';
      $qualif_name = '-';
      if ($qualification) {
        $qualif_icon = $qualification->icon ? EasyThumbnailImage::thumbnailFileUrl(
            '@root' . $qualification->icon,
            35,
            50,
            EasyThumbnailImage::THUMBNAIL_INSET
          ) : '';
        $qualif_name = $qualification->name;
      }

      $referals[] = [
        'id' => $child->id,
        'account_id' => $child->account_id,
        'email' => $child->email,
        'phone' => $child->phone,
        'picture' => $child->picture ? EasyThumbnailImage::thumbnailFileUrl(
            '@root' . $child->picture,
            100,
            100,
            EasyThumbnailImage::THUMBNAIL_INSET
        ) : '',
        'ref_link' => $child->ref_link,
        'first_name' => $child->first_name,
        'path' => $child->path,
        'level' => (int)$child->level,
        'childs_present' => (bool)$child->childs_present,
        'childs_count' => $child->getReferalsCount(),
        'level_percent' => $child->level_percent,
        'boughts' => (float)$child->boughts,
        'month_boughts' => (float)$child->month_boughts,
        'boughts_str' => number_format($child->boughts, 2, '.', ' '),
        'date' => date('d.m.Y', (int)$child->created_at),
        'license_till' => $license_till ? date('d.m.Y', (int)$license_till) : '-',
        'qualification' => $qualif_name,
        'qualif_icon' => $qualif_icon,
      ];
    }
    return $referals;
  }

  public function actionIndex()
  {
    return [
      'success' => 1,
      'referals' => $this->_populateFields(Customer::selectClientChilds()->all()),
    ];
  }

  public function actionTotals()
  {
    $model = Yii::$app->user->identity;

    $totals = $model->getReferalsCountByLevels();
    $total_count = 0;
    $month_total_count = 0;
    $active_total_count = 0;
    $active_month_total_count = 0;
    foreach ($totals as $total) {
      $total_count += $total['all'];
      $month_total_count += $total['month'];
      $active_total_count += $total['all_active'];
      $active_month_total_count += $total['month_active'];;
    }

    $list = $this->_pictureThumbnails($model->getClientsAllChilds($model->id, true));
    $active_list = [];

    $turnover_all = 0;
    $turnover_month = 0;

    foreach ($list as $item) {
      $turnover_all += (float)$item['boughts'];
      $turnover_month += (float)$item['month_boughts'];
      if ((float)$item['boughts']) {
        $active_list[] = $item;
      }
    }

    $clientTotals = [
      'registrations' => [
        'list' => $list,
        'total' => $total_count,
        'month_total' => $month_total_count,
      ],
      'active' => [
        'list' => $active_list,
        'total' => $active_total_count,
        'month_total' => $active_month_total_count,
      ],
      'turnover' => [
        'total' => (float)$turnover_all ? number_format($turnover_all, 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$turnover_month ? number_format($turnover_month, 2, '.', ' ') . '&nbsp;$' : '—',
      ],
    ];

    return [
      'success' => 1,
      'clientTotals' => $clientTotals,
      'referalTotals' => $totals,
      'referalsCount' => $total_count,
    ];
  }

  public function actionChilds()
  {

    $parent_path = Yii::$app->request->post('parent_path', '0');

    return [
      'success' => 1,
      'childs' => $this->_populateFields(Customer::selectClientChilds($parent_path)->all()),
    ];

  }

  public function actionChildPayments($id)
  {

    $clientsSearchModel = new PaymentSearch();
    $models = $clientsSearchModel->payments($id);

    $payments = [];
    foreach ($models as $item) {
      $attrs = $item->attributes;
      $attrs['created'] = date('d.m.Y', $item->created_at);
      $attrs['botname'] = $item->botname;
      $attrs['amount'] = number_format($item->amount, 2, '.', ' ');
      $attrs['paybacksum'] = number_format($item->paybacksum, 2, '.', ' ');
      $attrs['level'] = $item->level;
      $payments[] = $attrs;
    }
 
    return [
      'success' => 1,
      'payments' => $payments,
    ];

  }
}