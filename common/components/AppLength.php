<?php
  
namespace common\components;

use common\models\LengthClass;
use Yii;
use yii\base\Component;
  
class AppLength extends Component {
  
  private $lengths = [];

  public function Init()
  {
    $list = LengthClass::find()
      ->alias('lc')
      ->select([
        'lc.*',
        'lcd.title',
        'lcd.unit'
      ])
      ->leftJoin('oc_length_class_description lcd', 'lcd.length_class_id = lc.id AND lcd.language_id = "' . Yii::$app->language . '"')
      ->all();

    foreach ($list as $item) {
			$this->lengths[$item->id] = [
				'id'            => (int)$item->id,
				'title'         => $item->title,
				'unit'          => $item->unit,
				'value'         => (float)$item->value,
      ];
    }
  }

  public function getList()
  {
    $list = [];
    foreach ($this->lengths as $id => $item) {
      $list[$id] = $item['title'];
    }

    return $list;
  }

	public function convert($value, $from, $to) {
		if ($from == $to) {
			return $value;
		}

		if (isset($this->lengths[$from])) {
			$from = $this->lengths[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->lengths[$to])) {
			$to = $this->lengths[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $length_class_id, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->lengths[$length_class_id])) {
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->lengths[$length_class_id]['unit'];
		} else {
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($length_class_id) {
		if (isset($this->lengths[$length_class_id])) {
			return $this->lengths[$length_class_id]['unit'];
		} else {
			return '';
		}
	}
}