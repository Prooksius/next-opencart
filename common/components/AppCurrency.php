<?php
  
namespace common\components;

use common\models\Currency;
use Yii;
use yii\base\Component;
  
class AppCurrency extends Component {
  
  private $currencies = [];
  private $current = '';

  public function Init()
  {
    $list = Currency::find()->all();
    foreach ($list as $currency) {
			$this->currencies[$currency->code] = [
				'id'            => (int)$currency->id,
				'title'         => $currency->title,
				'symbol_left'   => $currency->symbol_left,
				'symbol_right'  => $currency->symbol_right,
				'decimal_place' => $currency->decimal_place,
				'value'         => (float)$currency->value,
      ];
    }

    if (Yii::$app->id == 'app-backend') {
      $this->current = Yii::$app->shopConfig->getParam('currency');
    }

  }

  public function getCurrent()
  {
    return $this->current;
  }

  public function setCurrent($value)
  {
    $this->current = $value;
  }

  public function getList()
  {
    $list = [];
    foreach ($this->currencies as $code => $item) {
      $list[$code] = $item['title'];
    }

    return $list;
  }

  public function format($number, $currency, $value = '', $format = true) {
		$symbol_left = !empty($this->currencies[$currency]['symbol_left']) ? $this->currencies[$currency]['symbol_left'] : '';
		$symbol_right = !empty($this->currencies[$currency]['symbol_right']) ? $this->currencies[$currency]['symbol_right'] : '';
		$decimal_place = !empty($this->currencies[$currency]['decimal_place']) ? $this->currencies[$currency]['decimal_place'] : '';

		if (!$value) {
			$value = $this->currencies[$currency]['value'];
		}

		$amount = $value ? (float)$number * $value : (float)$number;
		
		$amount = round($amount, (int)$decimal_place);
		
		if (!$format) {
			return $amount;
		}

		$string = '';

		if ($symbol_left) {
			$string .= $symbol_left;
		}

		$string .= number_format($amount, (int)$decimal_place, '.', '&nbsp;');

		if ($symbol_right) {
			$string .= $symbol_right;
		}

		return $string;
	}

	public function convert($value, $from, $to) {
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}
	
	public function getId($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['id'];
		} else {
			return 0;
		}
	}

	public function getSymbolLeft($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'];
		} else {
			return '';
		}
	}

	public function getSymbolRight($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}

	public function getDecimalPlace($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['decimal_place'];
		} else {
			return 0;
		}
	}

	public function getValue($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0;
		}
	}

	public function has($currency) {
		return isset($this->currencies[$currency]);
	}  
}