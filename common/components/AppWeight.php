<?php
  
namespace common\components;

use common\models\WeightClass;
use Yii;
use yii\base\Component;
  
class AppWeight extends Component {
  
  private $weights = [];

  public function Init()
  {
    $list = WeightClass::find()
      ->alias('wc')
      ->select([
        'wc.*',
        'wcd.title',
        'wcd.unit'
      ])
      ->leftJoin('oc_weight_class_description wcd', 'wcd.weight_class_id = wc.id AND wcd.language_id = "' . Yii::$app->language . '"')
      ->all();
      
    foreach ($list as $item) {
			$this->weights[$item->id] = [
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
    foreach ($this->weights as $id => $item) {
      $list[$id] = $item['title'];
    }

    return $list;
  }

	public function convert($value, $from, $to) {
		if ($from == $to) {
			return $value;
		}

		if (isset($this->weights[$from])) {
			$from = $this->weights[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->weights[$to])) {
			$to = $this->weights[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $weight_class_id, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->weights[$weight_class_id])) {
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->weights[$weight_class_id]['unit'];
		} else {
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($weight_class_id) {
		if (isset($this->weights[$weight_class_id])) {
			return $this->weights[$weight_class_id]['unit'];
		} else {
			return '';
		}
	}
}