<?php

namespace frontend\models;

use Yii;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "oc_category".
 */
class Category extends \yii\db\ActiveRecord
{

  public $name;
  public $description;
  public $meta_title;
  public $meta_h1;
  public $meta_description;
  public $meta_keyword;
  public $products_count;

	private static $filter_count = null;
	private static $get_attributes = [];
	private static $get_options = [];
	private static $get_filters = [];
	private static $get_brands = [];
	private static $get_colors = [];
	private static $get_price = [];
	private static $saved_filters = [];
	private static $brand_name = '';
	private static $color_name = '';

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_category';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'sort_order', 'parent_id', 'top', 'status'], 'integer'],
      [['alias', 'image'], 'string'],
    ];
  }

  public static function getCategories($parent_id = 0, $limit = 0, $filter_params = null)
  {
		if (!is_null($filter_params) && is_null(self::$filter_count)) {
      self::_getFieldsData($filter_params);
    }

    $query = self::find()
      ->alias('c')
      ->select([
        'c.*',
        'cd.*',
      ])
      ->leftJoin('oc_category_description cd', '(cd.category_id = c.id AND cd.language_id = "' . \Yii::$app->language . '")')
      ->where([
        'c.parent_id' => $parent_id,
      ])
      ->orderBy([
        'c.sort_order' => SORT_ASC,
        'LCASE(cd.name)' => SORT_ASC
      ]);

      if ((int)$limit) {
        $query->limit($limit);
      }

      $childs = $query->asArray()->all();

      if (!is_null($filter_params)) {

        $filter_data = array(
          'filter_sub_category' => true,
        );
        foreach (self::$get_attributes as $get_attribute) {
          $filter_data[$get_attribute] = $filter_params[$get_attribute];
        }
        foreach (self::$get_options as $get_option) {
          $filter_data[$get_option] = $filter_params[$get_option];
        }
        foreach (self::$get_filters as $get_filter) {
          $filter_data[$get_filter] = $filter_params[$get_filter];
        }
        foreach (self::$get_brands as $get_brand) {
          $filter_data[$get_brand] = $filter_params[$get_brand];
        }
        foreach (self::$get_colors as $get_color) {
          $filter_data[$get_color] = $filter_params[$get_color];
        }
        foreach (self::$get_price as $prices) {
          $filter_data['filter_'.$prices] = $filter_params[$prices];
        }

        foreach ($childs as &$child) {

          $filter_data['filter_category_id'] = $child['id'];

          $child['products_count'] = Product::getTotalProducts($filter_data);
        }
      }

      return $childs;
  }

  public static function getBreadcrumbs(array $slugs, string $lastSlug)
  {
    $breadcrumbs = [
      [
        'title' => Translation::getTranslation('Catalog'),
        'href' => !empty($slugs) || $lastSlug ? '/catalog' : '',
      ],
    ];
    $href = ['catalog'];
    foreach ($slugs as $item) {
      $bread_category = self::getCategoryBySlug($item);
      if (!$bread_category) {
        throw new \Exception(Yii::t('app', 'Category not found'));
      }
      $href[] = $item;
      $breadcrumbs[] = [
        'title' => $bread_category['name'],
        'href' => '/'. implode('/', $href),
      ];
    }

    if ($lastSlug) {
      $current_category = self::getCategoryBySlug($lastSlug);
      if (!$current_category) {
        throw new \Exception(Yii::t('app', 'Category not found'));
      }
      $breadcrumbs[] = [
        'title' => $current_category['name'],
        'href' => '',
      ];
    }

    return $breadcrumbs;
  }

  public static function getCategory($id)
  {
    return self::_getCategory(['id' => $id]);
  }

  public static function getCategoryBySlug($slug)
  {
    return self::_getCategory(['alias' => $slug]);
  }

  private static function _getCategory($param)
  {
    $query = self::find()
      ->alias('c')
      ->select([
        'c.*',
        'cd.*'
      ])
      ->leftJoin('oc_category_description cd', '(cd.category_id = c.id AND cd.language_id = "' . \Yii::$app->language . '")');

      if (!empty($param['id'])) {
        $query->where(['id' => $param['id']]);
        return $query->asArray()->one();
      } elseif (!empty($param['alias'])){
        $query->where(['alias' => $param['alias']]);
        return $query->asArray()->one();
      }
      return false;
  }

  public static function getCategoryColors($data, $color_name) {
    $results = [];

    $colors = [];
    $attributes = [];
    $options = [];
    $filters = [];
    $brands = [];

    $all_attrs = Attribute::getAllAttributes();

    foreach ($all_attrs as $result) {
      if (!empty($data[$result['alias']])) {
        $attributes[] = $result['alias'];
      }
    }

    $all_opts = Option::getAllOptions();

    foreach ($all_opts as $result) {
      if (!empty($data[$result['alias']])) {
        $options[] = $result['alias'];
      }
    }

    $all_filters = FilterGroup::getAllFilters();

    foreach ($all_filters as $result) {
      if (!empty($data[$result['alias']])) {
        $filters[] = $result['alias'];
      }
    }

    if (!empty($data['brand'])) {
        $brands[] = 'brand';
    }

		$price_min = 0;
		$price_max = 0;
    if (!empty($data['filter_price'])) {
      $prices = explode('-', $data['filter_price']);
			$price_min = ((float)$prices[0] 
                      ? (float)Yii::$app->currency->convert((float)$prices[0], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
			$price_max = ((float)$prices[1] 
                      ? (float)Yii::$app->currency->convert((float)$prices[1], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
    }		

    $sql = "
		SELECT 
			pcl.id AS pcolor_id, 
			pcl.image AS image, 
			pcld.name AS color_name, 
			COUNT(DISTINCT p.id) AS quantity, 
			pcl.alias AS alias
			
			FROM oc_pcolor pcl 
			LEFT JOIN oc_pcolor_description pcld ON (pcl.id = pcld.pcolor_id)";
		
		if ($price_min || $price_max) {
		
			$sql .= " 
				LEFT JOIN ( 
					SELECT 
						p1.manufacturer_id,
						p1.id,
						p1.pcolor_id,
						p1.price AS price,
						( SELECT price 
              FROM oc_product_discount pd2 
              WHERE 
                pd2.product_id = p1.id AND 
                pd2.customer_group_id = '1' AND 
                pd2.quantity = '1' AND 
                ((pd2.date_start = 0 OR pd2.date_start < UNIX_TIMESTAMP(NOW())) AND 
                (pd2.date_end = 0 OR pd2.date_end > UNIX_TIMESTAMP(NOW()))) 
              ORDER BY pd2.priority ASC, pd2.price ASC 
              LIMIT 1
            ) AS discount, 
						( SELECT price FROM oc_product_special ps 
              WHERE 
                ps.product_id = p1.id AND 
                ps.customer_group_id = '1' AND 
                ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND 
                (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
              ORDER BY ps.priority ASC, ps.price ASC 
              LIMIT 1
            ) AS special 
					FROM 
						oc_product p1
					WHERE
						p1.status = '1'";

			if ($price_min & $price_max) {
				$sql .= " 
					HAVING (
						CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . " AND 
						CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
					)";
			} elseif ($price_min) {
				$sql .= " 
					HAVING (
						CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . "
					)";
			} elseif ($price_max) {
				$sql .= " 
					HAVING (
						CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
					)";
			}
			$sql .= " 	
				) AS p ON (pcl.id = p.pcolor_id)";

		} else {
			$sql .= " 
				LEFT JOIN oc_product p ON (pcl.id = p.pcolor_id)";			
		}
		
    if ($data['filter_category_id']) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " 
						LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id) 
						LEFT JOIN oc_category_path cp ON (cp.category_id = p2c.category_id)"; 
			} else {
        $sql .= " LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id)";
			}
		}

    if (!empty($attributes)) {
      foreach ($attributes as $key => $attribute) {
        $sql .= " 
          LEFT JOIN oc_product_attribute pa" . ($key + 1) . " ON 
            (p.id = pa" . ($key + 1) . ".product_id AND pa" . ($key + 1) . ".language_id = '" . \Yii::$app->language . "') 
          LEFT JOIN oc_attribute a" . ($key + 1) . " ON 
            (pa" . ($key + 1) . ".attribute_id = a" . ($key + 1) . ".id)";
        }
    }
    if (!empty($options)) {
      foreach ($options as $key => $option) {
        $sql .= " 
          LEFT JOIN oc_product_option_value pov" . ($key + 1) . " ON 
            (p.id = pov" . ($key + 1) . ".product_id) 
          LEFT JOIN oc_option_value ov" . ($key + 1) . " ON 
            (ov" . ($key + 1) . ".id = pov" . ($key + 1) . ".option_value_id) 
          LEFT JOIN oc_option o" . ($key + 1) . " ON 
            (o" . ($key + 1) . ".id = ov" . ($key + 1) . ".option_id)";
      }
    }
    if (!empty($filters)) {
      foreach ($filters as $key => $filter) {
        $sql .= " 
          LEFT JOIN oc_product_filter pf" . ($key + 1) . " ON 
            (p.id = pf" . ($key + 1) . ".product_id)
          LEFT JOIN oc_filter f" . ($key + 1) . " 
            ON (pf" . ($key + 1) . ".filter_id = f" . ($key + 1) . ".id)
          LEFT JOIN oc_filter_group fg" . ($key + 1) . " 
            ON(f" . ($key + 1) . ".filter_group_id = fg" . ($key + 1) . ".id)";
      }
    }

		if (!empty($brands)) {
			$sql .= " LEFT JOIN oc_manufacturer m ON (p.manufacturer_id = m.id)";
		}
		
		$sql .= " 
			WHERE 
				pcld.language_id = '" . \Yii::$app->language . "'";

    foreach ($attributes as $key => $attribute) {
      $sql .= " AND a" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($attribute);
      if (strpos($data[$attribute], ',')) {
        $arr_attribute = explode(',', $data[$attribute]);
        $str = Yii::$app->db->quoteValue(array_shift($arr_attribute));
        foreach ($arr_attribute as $item) {
            $str .= ", " . Yii::$app->db->quoteValue($item);
        }
        $sql .= " AND pa" . ($key + 1) . ".alias IN (" .  $str . ")";
      } elseif (strpos($data[$attribute], '[') === 0 && strpos($data[$attribute], '-') !== false) {
        $attr_temp = trim($data[$attribute], '[]');
        $arr_attribute = explode('-', $attr_temp);
        if ($arr_attribute[0] && $arr_attribute[1]) {
          $sql .= " AND (CAST(pa" . ($key + 1) . ".alias AS DECIMAL) BETWEEN " . (float)$arr_attribute[0] . " AND " . (float)$arr_attribute[1] . ")";
        } elseif ($arr_attribute[0]) {
          $sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) >= " . (float)$arr_attribute[0];
        } elseif ($arr_attribute[1]) {
          $sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) <= " . (float)$arr_attribute[1];
        }
      } else {
        $sql .= " AND pa" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$attribute]);
      }
    }

    foreach ($options as $key => $option) {
      $sql .= " AND o" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($option);
      if (strpos($data[$option], ',')) {
        $arr_option = explode(',', $data[$option]);
        $str = Yii::$app->db->quoteValue(array_shift($arr_option));
        foreach ($arr_option as $item) {
            $str .= ", " . Yii::$app->db->quoteValue($item);
        }
        $sql .= " AND ov" . ($key + 1) . ".alias IN (" .  $str . ")";
      } else {
        $sql .= " AND ov" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$option]);
      }
    }

    foreach ($filters as $key => $filter) {
      $sql .= " AND fg" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($filter);
      if (strpos($data[$filter], ',')) {
        $arr_filter = explode(',', $data[$filter]);
        $str = Yii::$app->db->quoteValue(array_shift($arr_filter));
        foreach ($arr_filter as $item) {
            $str .= ", " . Yii::$app->db->quoteValue($item);
        }
        $sql .= " AND f" . ($key + 1) . ".alias IN (" .  $str . ")";
      } else {
        $sql .= " AND f" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$filter]);
      }
    }

    if (!empty($brands)) {
      if (strpos($data['brand'], ',')) {
        $brand_filter = explode(',', $data['brand']);
        $str = Yii::$app->db->quoteValue(array_shift($brand_filter));
        foreach ($brand_filter as $item) {
            $str .= ", " . Yii::$app->db->quoteValue($item);
        }
        $sql .= " AND m.alias IN (" .  $str . ")";
      } else {
        $sql .= " AND m.alias = " .  Yii::$app->db->quoteValue($data['brand']);
      }
    }

		if ($data['filter_category_id']) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		if (!$price_min && !$price_max) {
			$sql .= " AND p.status = '1'";
		}

		if (!(int)Yii::$app->shopConfig->getParam('show_zero_quantity')) {
			$sql .= " AND p.quantity > 0";
		}

		$sql .= " GROUP BY pcl.id";
				
		$sql .= " ORDER BY pcl.sort_order ASC";
		
		$color_records = Yii::$app->db
      ->createCommand($sql)
      ->queryAll();

    foreach ($color_records as $result) {
			$results[$color_name.';color'][] = [
        'filter_id'       => $result['pcolor_id'],
				'color_value' 	  => $result['color_name'],
				'color_eng_value' => $result['alias'],
				'color_image'     => $result['image'],
				'quantity'        => $result['quantity'],
      ];
    }
    return $results;
  }

  public static function getCategoryManufacturers($data, $brand_name) {
    $results = [];

    $colors = [];
    $attributes = [];
    $options = [];
    $filters = [];
    $brands = [];

    $all_attrs = Attribute::getAllAttributes();

    foreach ($all_attrs as $result) {
      if (!empty($data[$result['alias']])) {
        $attributes[] = $result['alias'];
      }
    }

    $all_opts = Option::getAllOptions();

    foreach ($all_opts as $result) {
      if (!empty($data[$result['alias']])) {
        $options[] = $result['alias'];
      }
    }

    $all_filters = FilterGroup::getAllFilters();

    foreach ($all_filters as $result) {
      if (!empty($data[$result['alias']])) {
        $filters[] = $result['alias'];
      }
    }

		if (!empty($data['color'])) {
			$colors[] = 'color';
		}

		$price_min = 0;
		$price_max = 0;
    if (!empty($data['filter_price'])) {
      $prices = explode('-', $data['filter_price']);
			$price_min = ((float)$prices[0] 
                      ? (float)Yii::$app->currency->convert((float)$prices[0], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
			$price_max = ((float)$prices[1] 
                      ? (float)Yii::$app->currency->convert((float)$prices[1], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
    }		

		$sql = "
		SELECT 
			m.id AS manufacturer_id, 
			md.name AS brand_name, 
			COUNT(DISTINCT p.id) AS quantity, 
			m.alias AS alias
			
    FROM oc_manufacturer m 
    LEFT JOIN oc_manufacturer_description md ON (m.id = md.manufacturer_id)";
		
		if ($price_min || $price_max) {
		
			$sql .= " 
				LEFT JOIN ( 
					SELECT 
						p1.manufacturer_id,
						p1.id,
						p1.pcolor_id,
						p1.price AS price,
						p1.quantity AS quantity,
						( SELECT price 
              FROM oc_product_discount pd2 
              WHERE 
                pd2.product_id = p1.id AND 
                pd2.customer_group_id = '1' AND 
                pd2.quantity = '1' AND 
                ((pd2.date_start = 0 OR pd2.date_start < UNIX_TIMESTAMP(NOW())) AND 
                (pd2.date_end = 0 OR pd2.date_end > UNIX_TIMESTAMP(NOW()))) 
              ORDER BY pd2.priority ASC, pd2.price ASC 
              LIMIT 1
            ) AS discount, 
						( SELECT price FROM oc_product_special ps 
              WHERE 
                ps.product_id = p1.id AND 
                ps.customer_group_id = '1' AND 
                ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND 
                (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
              ORDER BY ps.priority ASC, ps.price ASC 
              LIMIT 1
            ) AS special 
					FROM 
						oc_product p1
					WHERE
						p1.status = '1'";

			if ($price_min & $price_max) {
				$sql .= " 
					HAVING (
						CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . " AND 
						CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
					)";
			} elseif ($price_min) {
				$sql .= " 
					HAVING (
						CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . "
					)";
			} elseif ($price_max) {
				$sql .= " 
					HAVING (
						CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
					)";
			}
			$sql .= " 	
				) AS p ON (m.id = p.manufacturer_id)";

		} else {
			$sql .= " 
				LEFT JOIN oc_product p ON (m.id = p.manufacturer_id)";			
		}

    if ($data['filter_category_id']) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " 
						LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id) 
						LEFT JOIN oc_category_path cp ON (cp.category_id = p2c.category_id)"; 
			} else {
        $sql .= " LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id)";
			}
		}

    if (!empty($colors)) {
			$sql .= " 	
				LEFT JOIN oc_pcolor pcl ON (pcl.id = p.pcolor_id)"; 
		}
		
    if (!empty($attributes)) {
      foreach ($attributes as $key => $attribute) {
        $sql .= " 
          LEFT JOIN oc_product_attribute pa" . ($key + 1) . " ON 
            (p.id = pa" . ($key + 1) . ".product_id AND pa" . ($key + 1) . ".language_id = '" . \Yii::$app->language . "') 
          LEFT JOIN oc_attribute a" . ($key + 1) . " ON 
            (pa" . ($key + 1) . ".attribute_id = a" . ($key + 1) . ".id)";
        }
    }
    if (!empty($options)) {
      foreach ($options as $key => $option) {
        $sql .= " 
          LEFT JOIN oc_product_option_value pov" . ($key + 1) . " ON 
            (p.id = pov" . ($key + 1) . ".product_id) 
          LEFT JOIN oc_option_value ov" . ($key + 1) . " ON 
            (ov" . ($key + 1) . ".id = pov" . ($key + 1) . ".option_value_id) 
          LEFT JOIN oc_option o" . ($key + 1) . " ON 
            (o" . ($key + 1) . ".id = ov" . ($key + 1) . ".option_id)";
      }
    }
    if (!empty($filters)) {
      foreach ($filters as $key => $filter) {
        $sql .= " 
          LEFT JOIN oc_product_filter pf" . ($key + 1) . " ON 
            (p.id = pf" . ($key + 1) . ".product_id)
          LEFT JOIN oc_filter f" . ($key + 1) . " 
            ON (pf" . ($key + 1) . ".filter_id = f" . ($key + 1) . ".id)
          LEFT JOIN oc_filter_group fg" . ($key + 1) . " 
            ON (f" . ($key + 1) . ".filter_group_id = fg" . ($key + 1) . ".id)";
      }
    }
		
		$sql .= " 
			WHERE 
				md.language_id = '" . \Yii::$app->language . "'";

    foreach ($attributes as $key => $attribute) {
      $sql .= " AND a" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($attribute);
      if (strpos($data[$attribute], ',')) {
        $arr_attribute = explode(',', $data[$attribute]);
        $str = Yii::$app->db->quoteValue(array_shift($arr_attribute));
        foreach ($arr_attribute as $item) {
            $str .= ", " . Yii::$app->db->quoteValue($item);
        }
        $sql .= " AND pa" . ($key + 1) . ".alias IN (" .  $str . ")";
      } elseif (strpos($data[$attribute], '[') === 0 && strpos($data[$attribute], '-') !== false) {
        $attr_temp = trim($data[$attribute], '[]');
        $arr_attribute = explode('-', $attr_temp);
        if ($arr_attribute[0] && $arr_attribute[1]) {
          $sql .= " AND (CAST(pa" . ($key + 1) . ".alias AS DECIMAL) BETWEEN " . (float)$arr_attribute[0] . " AND " . (float)$arr_attribute[1] . ")";
        } elseif ($arr_attribute[0]) {
          $sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) >= " . (float)$arr_attribute[0];
        } elseif ($arr_attribute[1]) {
          $sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) <= " . (float)$arr_attribute[1];
        }
      } else {
        $sql .= " AND pa" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$attribute]);
      }
    }

    foreach ($options as $key => $option) {
      $sql .= " AND o" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($option);
      if (strpos($data[$option], ',')) {
        $arr_option = explode(',', $data[$option]);
        $str = Yii::$app->db->quoteValue(array_shift($arr_option));
        foreach ($arr_option as $item) {
            $str .= ", " . Yii::$app->db->quoteValue($item);
        }
        $sql .= " AND ov" . ($key + 1) . ".alias IN (" .  $str . ")";
      } else {
        $sql .= " AND ov" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$option]);
      }
    }

    foreach ($filters as $key => $filter) {
      $sql .= " AND fg" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($filter);
      if (strpos($data[$filter], ',')) {
        $arr_filter = explode(',', $data[$filter]);
        $str = Yii::$app->db->quoteValue(array_shift($arr_filter));
        foreach ($arr_filter as $item) {
            $str .= ", " . Yii::$app->db->quoteValue($item);
        }
        $sql .= " AND f" . ($key + 1) . ".alias IN (" .  $str . ")";
      } else {
        $sql .= " AND f" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$filter]);
      }
    }

    if (!empty($colors)) {
      if (strpos($data['color'], ',')) {
        $arr_color = explode(',', $data['color']);
        $str = Yii::$app->db->quoteValue(array_shift($arr_color));
        foreach ($arr_color as $item) {
          $str .= ", " . Yii::$app->db->quoteValue($item);
        }
				$sql .= " AND pcl.alias IN (" . $str . ")";
			} else {
				$str = "'" . $data['color'] . "'";
				$sql .= " AND pcl.alias = " . Yii::$app->db->quoteValue($data['color']);
			}
		}

    if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		if (!$price_min && !$price_max) {
			$sql .= " AND p.status = '1'";
		}

		if (!(int)Yii::$app->shopConfig->getParam('show_zero_quantity')) {
			$sql .= " AND p.quantity > 0";
		}

		$sql .= " GROUP BY m.id";
				
		$sql .= " ORDER BY md.name ASC";
		
		file_put_contents( FileHelper::normalizePath(YII::getAlias('@root') . '/1122-brands.txt'), $sql);
    $brand_records = \Yii::$app->db
      ->createCommand($sql)
      ->queryAll();

    foreach ($brand_records as $result) {
			$results[$brand_name.';brand'][] = [
        'filter_id'       => $result['manufacturer_id'],
				'brand_value' 	  => $result['brand_name'],
				'brand_eng_value' => $result['alias'],
				'quantity'        => $result['quantity'],
      ];
    }
    return $results;
  }

  public static function getCategoryAttributes($data) {
    $results = [];

    $colors = [];
    $attributes = [];
    $options = [];
    $filters = [];
    $brands = [];

    $all_attrs = Attribute::getAllAttributes();

    foreach ($all_attrs as $result) {
      if (!empty($data[$result['alias']])) {
        $attributes[] = $result['alias'];
      }
    }

    $all_opts = Option::getAllOptions();

    foreach ($all_opts as $result) {
      if (!empty($data[$result['alias']])) {
        $options[] = $result['alias'];
      }
    }

    $all_filters = FilterGroup::getAllFilters();

    foreach ($all_filters as $result) {
      if (!empty($data[$result['alias']])) {
        $filters[] = $result['alias'];
      }
    }

		if (!empty($data['color'])) {
			$colors[] = 'color';
		}

    if (!empty($data['brand'])) {
        $brands[] = 'brand';
    }

		$price_min = 0;
		$price_max = 0;
    if (!empty($data['filter_price'])) {
      $prices = explode('-', $data['filter_price']);
			$price_min = ((float)$prices[0] 
                      ? (float)Yii::$app->currency->convert((float)$prices[0], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
			$price_max = ((float)$prices[1] 
                      ? (float)Yii::$app->currency->convert((float)$prices[1], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
    }		

		$sql = "
			SELECT DISTINCT
				a.id AS attribute_id,
				a.alias AS alias_name, 
				a.filter_sort_order AS filter_sort_order,
				a.open_filter AS open_filter,
				a.value_type AS value_type,
				a.value_sort AS value_sort,
				a.icon AS icon,
				a.show_filter AS show_filter,
				ad.name AS attr_name,
				ad.description AS attr_desc 
			FROM oc_attribute a
			LEFT JOIN oc_attribute_description ad ON (a.id = ad.attribute_id)
			LEFT JOIN oc_product_attribute pa ON (a.id = pa.attribute_id)
			LEFT JOIN oc_product p ON (pa.product_id = p.id)";

		if ($data['filter_category_id']) {
      if (!empty($data['filter_sub_category'])) {
        $sql .= " 
            LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id) 
            LEFT JOIN oc_category_path cp ON (cp.category_id = p2c.category_id)"; 
      } else {
        $sql .= " LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id)";
      }
		}
		
		$sql .= "
			WHERE 
				a.show_filter = '1' AND 
				ad.language_id = '" . \Yii::$app->language . "' AND 
				p.status = '1'";

		if ($data['filter_category_id']) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
//			$sql .= " AND a.id NOT IN (SELECT attribute_id FROM oc_category_attribute WHERE category_id = '" . (int)$data['filter_category_id'] . "')";
		}
		
		$sql .= " 
			GROUP BY 
				attribute_id";
				
		$sql .= "
			ORDER BY 
				ad.name";
		
		try {
      $attr_records = \Yii::$app->db
        ->createCommand($sql)
        ->queryAll();
		} catch (\Exception $e) {
			echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
		}	
				
		foreach ($attr_records as $sel_attribute) {
			
			$attribute_data = [];

			$sql = "
      SELECT 
				pa.product_id AS attr_product_id ,
				pa.attribute_id AS attribute_id ,
				pa.text AS attr_value, 
				pa.alias AS alias_value, 
				COUNT(DISTINCT p.id) AS quantity";

			$sql .= "
				FROM oc_product_attribute pa"; 

			if ($price_min || $price_max) {
			
				$sql .= " 
					LEFT JOIN ( 
						SELECT 
							p1.manufacturer_id AS manufacturer_id,
							p1.id AS product_id,
							p1.pcolor_id AS pcolor_id,
							p1.price AS price,
							p1.quantity AS quantity,
							( SELECT price 
                FROM oc_product_discount pd2 
                WHERE 
                  pd2.product_id = p1.id AND 
                  pd2.customer_group_id = '1' AND 
                  pd2.quantity = '1' AND 
                  ((pd2.date_start = 0 OR pd2.date_start < UNIX_TIMESTAMP(NOW())) AND 
                  (pd2.date_end = 0 OR pd2.date_end > UNIX_TIMESTAMP(NOW()))) 
                ORDER BY pd2.priority ASC, pd2.price ASC 
                LIMIT 1
              ) AS discount, 
							( SELECT price 
                FROM oc_product_special ps 
                WHERE 
                  ps.product_id = p1.id AND 
                  ps.customer_group_id = '1' AND 
                  ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND 
                  (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
                ORDER BY ps.priority ASC, ps.price ASC 
                LIMIT 1
              ) AS special 
						FROM 
							oc_product p1
						WHERE
							p1.status = '1'";
						
				if ($price_min & $price_max) {
					$sql .= " 
						HAVING (
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . " AND 
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
						)";
				} elseif ($price_min) {
					$sql .= " 
						HAVING (
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . "
						)";
				} elseif ($price_max) {
					$sql .= " 
						HAVING (
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
						)";
				}
				$sql .= " 	
					) AS p ON (pa.product_id = p.id)";

			} else {
				$sql .= "
					LEFT JOIN oc_product p ON (pa.product_id = p.id)"; 
			}			
										
			if ($data['filter_category_id']) {
        if (!empty($data['filter_sub_category'])) {
          $sql .= " 
              LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id) 
              LEFT JOIN oc_category_path cp ON (cp.category_id = p2c.category_id)"; 
        } else {
          $sql .= " LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id)";
        }
			}
			
			if (!empty($colors)) {
				$sql .= "
					LEFT JOIN oc_pcolor pcl ON (pcl.id = p.pcolor_id)"; 
			}
		
			if (!empty($attributes)) {
				foreach ($attributes as $key => $attribute) {
					if ($attribute != $sel_attribute['alias_name']) {
						$sql .= " 
							LEFT JOIN oc_product_attribute pa" . ($key + 1) . " ON 
                (p.id = pa" . ($key + 1) . ".product_id AND pa" . ($key + 1) . ".language_id = '" . \Yii::$app->language . "')";
					}
				}
			}
			if (!empty($options)) {
				foreach ($options as $key => $option) {
					$sql .= " 
            LEFT JOIN oc_product_option_value pov" . ($key + 1) . " ON 
              (p.id = pov" . ($key + 1) . ".product_id) LEFT JOIN oc_option_value ov" . ($key + 1) . " ON 
              (ov" . ($key + 1) . ".id = pov" . ($key + 1) . ".option_value_id) 
            LEFT JOIN oc_option o" . ($key + 1) . " ON 
              (o" . ($key + 1) . ".id = ov" . ($key + 1) . ".option_id)";
				}
			}
			if (!empty($filters)) {
				foreach ($filters as $key => $filter) {
					$sql .= " 
            LEFT JOIN oc_product_filter pf" . ($key + 1) . " 
              ON (p.id = pf" . ($key + 1) . ".product_id)
					  LEFT JOIN oc_filter f" . ($key + 1) . " ON 
              (pf" . ($key + 1) . ".filter_id = f" . ($key + 1) . ".id)
					  LEFT JOIN oc_filter_group fg" . ($key + 1) . " ON 
              (f" . ($key + 1) . ".filter_group_id = fg" . ($key + 1) . ".id)";
				}
			}

			if (!empty($brands)) {
				$sql .= " LEFT JOIN oc_manufacturer m ON (p.manufacturer_id = m.id)";
			}
      if ($price_min || $price_max) {
        $sql .= ")";
      }
			
			$sql .= " WHERE pa.attribute_id = '" . (int)$sel_attribute['attribute_id'] . "' AND pa.language_id = '" . \Yii::$app->language . "'";

			foreach ($attributes as $key => $attribute) {
				if ($attribute != $sel_attribute['alias_name']) {
					if (strpos($data[$attribute], ',')) {
						$arr_attribute = explode(',', $data[$attribute]);
            $str = Yii::$app->db->quoteValue(array_shift($arr_attribute));
						foreach ($arr_attribute as $item) {
							$str .= ", " . Yii::$app->db->quoteValue($item);
						}
						$sql .= " AND pa" . ($key + 1) . ".alias IN (" . $str . ")";
					} elseif (strpos($data[$attribute], '[') === 0 && strpos($data[$attribute], '-') !== false) {
						$attr_temp = trim($data[$attribute], '[]');
						$arr_attribute = explode('-', $attr_temp);
						if ($arr_attribute[0] && $arr_attribute[1]) {
							$sql .= " AND (CAST(pa" . ($key + 1) . ".alias AS DECIMAL) BETWEEN " . (float)$arr_attribute[0] . " AND " . (float)$arr_attribute[1] . ")";
						} elseif ($arr_attribute[0]) {
							$sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) >= " . (float)$arr_attribute[0];
						} elseif ($arr_attribute[1]) {
							$sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) <= " . (float)$arr_attribute[1];
						}
					} else {
						$sql .= " AND pa" . ($key + 1) . ".alias = '" . (string)$data[$attribute] . "'";
					}
				}
			}

      foreach ($options as $key => $option) {
        $sql .= " AND o" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($option);
        if (strpos($data[$option], ',')) {
          $arr_option = explode(',', $data[$option]);
          $str = Yii::$app->db->quoteValue(array_shift($arr_option));
          foreach ($arr_option as $item) {
              $str .= ", " . Yii::$app->db->quoteValue($item);
          }
          $sql .= " AND ov" . ($key + 1) . ".alias IN (" .  $str . ")";
        } else {
          $sql .= " AND ov" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$option]);
        }
      }

      foreach ($filters as $key => $filter) {
        $sql .= " AND fg" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($filter);
        if (strpos($data[$filter], ',')) {
          $arr_filter = explode(',', $data[$filter]);
          $str = Yii::$app->db->quoteValue(array_shift($arr_filter));
          foreach ($arr_filter as $item) {
              $str .= ", " . Yii::$app->db->quoteValue($item);
          }
          $sql .= " AND f" . ($key + 1) . ".alias IN (" .  $str . ")";
        } else {
          $sql .= " AND f" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$filter]);
        }
      }

      if (!empty($brands)) {
        if (strpos($data['brand'], ',')) {
          $brand_filter = explode(',', $data['brand']);
          $str = Yii::$app->db->quoteValue(array_shift($brand_filter));
          foreach ($brand_filter as $item) {
              $str .= ", " . Yii::$app->db->quoteValue($item);
          }
          $sql .= " AND m.alias IN (" .  $str . ")";
        } else {
          $sql .= " AND m.alias = " .  Yii::$app->db->quoteValue($data['brand']);
        }
      }

			if ($data['filter_category_id']) {
				if (!empty($data['filter_sub_category'])) {
					$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
				} else {
					$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				}
			}
			
			if (!empty($colors)) {
				if (strpos($data['color'], ',')) {
					$arr_color = explode(',', $data['color']);
          $str = Yii::$app->db->quoteValue(array_shift($arr_color));
					foreach ($arr_color as $item) {
						$str .= ", " . Yii::$app->db->quoteValue($item);
					}
					$sql .= " AND pcl.alias IN (" . $str . ")";
				} else {
					$str = "'" . $data['color'] . "'";
					$sql .= " AND pcl.alias = " . Yii::$app->db->quoteValue($data['color']);
				}
			}

			if (!$price_min && !$price_max) {
				$sql .= " AND p.status = '1'";
			}

  		if (!(int)Yii::$app->shopConfig->getParam('show_zero_quantity')) {
				$sql .= " AND p.quantity > 0";
			}

			$sql .= " GROUP BY alias_value";
			
			if ($sel_attribute['value_sort'] == 1) {
				$sql .= " ORDER BY CAST(pa.text AS DECIMAL) ASC";
			} else {
				$sql .= " ORDER BY pa.text ASC";
			}

			//file_put_contents('1122-attrs-'.$sel_attribute['alias_name'].'.txt', $sql);
      $attr_records = Yii::$app->db
        ->createCommand($sql)
        ->queryAll();

			$min_value = 3999999999;
			$max_value = -3999999999;
			foreach ($attr_records as $result) {
				if ((float)$result['attr_value'] < $min_value) {
					$min_value = (float)$result['attr_value'];
				}
				if ((float)$result['attr_value'] > $max_value) {
					$max_value = (float)$result['attr_value'];
				}
				if ($sel_attribute['value_type'] == Attribute::VALUE_TYPE_CHECKBOX || $sel_attribute['value_type'] == Attribute::VALUE_TYPE_RADIO) {
					$attribute_data[] = [
						'filter_id' 	    => $result['attr_product_id'] . '-'. $result['attribute_id'],
						'attr_value' 	    => $result['attr_value'],
						'attr_eng_value'  => $result['alias_value'],
						'quantity' 		    => $result['quantity'],
          ];
				}
			}
			
			if ($sel_attribute['value_type'] == Attribute::VALUE_TYPE_RANGE) {
				$attribute_data = [
					'min_value' => $min_value,
					'max_value' => $max_value,
        ];
			}
			
			if ($attribute_data) {
				$results[] = [
					'filter_group_id' 	=> $sel_attribute['attribute_id'],
					'attr_name' 	 	    => $sel_attribute['attr_name'],
					'attr_desc' 	 	    => $sel_attribute['attr_desc'],
					'alias_name' 	 	    => $sel_attribute['alias_name'],
					'filter_sort_order' => $sel_attribute['filter_sort_order'],
					'open_filter' 		  => $sel_attribute['open_filter'],
					'value_type' 		    => $sel_attribute['value_type'],
					'show_filter' 	 	  => $sel_attribute['show_filter'],
					'show_menu' 	 	    => $sel_attribute['show_menu'],
					'icon' 				      => $sel_attribute['icon'],
					'attribute' 	 	    => $attribute_data,
        ];
			}
    }
    return $results;
  }

  public static function getCategoryOptions($data) {
    $results = [];

    $colors = [];
    $attributes = [];
    $options = [];
    $filters = [];
    $brands = [];

    $all_attrs = Attribute::getAllAttributes();

    foreach ($all_attrs as $result) {
      if (!empty($data[$result['alias']])) {
        $attributes[] = $result['alias'];
      }
    }

    $all_opts = Option::getAllOptions();

    foreach ($all_opts as $result) {
      if (!empty($data[$result['alias']])) {
        $options[] = $result['alias'];
      }
    }

    $all_filters = FilterGroup::getAllFilters();

    foreach ($all_filters as $result) {
      if (!empty($data[$result['alias']])) {
        $filters[] = $result['alias'];
      }
    }

		if (!empty($data['color'])) {
			$colors[] = 'color';
		}

    if (!empty($data['brand'])) {
        $brands[] = 'brand';
    }

		$price_min = 0;
		$price_max = 0;
    if (!empty($data['filter_price'])) {
      $prices = explode('-', $data['filter_price']);
			$price_min = ((float)$prices[0] 
                      ? (float)Yii::$app->currency->convert((float)$prices[0], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
			$price_max = ((float)$prices[1] 
                      ? (float)Yii::$app->currency->convert((float)$prices[1], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
    }		
		
		$sql = "
			SELECT DISTINCT 
				o.id AS option_id,
				o.alias AS alias_name,
				o.filter_sort_order AS filter_sort_order,
				o.open_filter AS open_filter,
				od.name AS opt_name
			FROM oc_option o
			LEFT JOIN oc_option_description od ON (o.id = od.option_id)
			LEFT JOIN oc_product_option po ON (o.id = po.option_id)
			LEFT JOIN oc_product p ON (po.product_id = p.id)";

		if ($data['filter_category_id']) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " 
						LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id) 
						LEFT JOIN oc_category_path cp ON (cp.category_id = p2c.category_id)"; 
			} else {
        $sql .= " LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id)";
			}
		}
		
		$sql .= "
			WHERE 
				od.language_id = '" . \Yii::$app->language . "' AND 
				p.status = '1'";
				
		if ($data['filter_category_id']) {
      if (!empty($data['filter_sub_category'])) {
        $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
      } else {
        $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
      }
		}

		$sql .= " 
			GROUP BY 
				option_id";

		$sql .= "
			ORDER BY 
				od.name";
		
    $option_records = Yii::$app->db
      ->createCommand($sql)
      ->queryAll();
		
		foreach ($option_records as $sel_option) {
			
			$option_data = [];

			$sql = "
      SELECT 
				ov.id AS option_value_id ,
				ovd.name AS opt_value,
				ov.alias AS alias_value,
				ov.color AS color_value,
				COUNT(DISTINCT p.id) AS quantity";

			$sql .= "
				FROM oc_option_value ov
				LEFT JOIN oc_option_value_description ovd ON (ovd.option_value_id = ov.id)
				LEFT JOIN oc_product_option_value pov ON (pov.option_value_id = ov.id)"; 

			if ($price_min || $price_max) {
			
				$sql .= " 
					LEFT JOIN ( 
						SELECT 
							p1.manufacturer_id AS manufacturer_id,
							p1.id AS product_id,
							p1.pcolor_id AS pcolor_id,
							p1.price AS price,
							p1.quantity AS quantity,
							( SELECT price 
                FROM oc_product_discount pd2 
                WHERE 
                  pd2.product_id = p1.id AND 
                  pd2.customer_group_id = '1' AND 
                  pd2.quantity = '1' AND 
                  ((pd2.date_start = 0 OR pd2.date_start < UNIX_TIMESTAMP(NOW())) AND 
                  (pd2.date_end = 0 OR pd2.date_end > UNIX_TIMESTAMP(NOW()))) 
                ORDER BY pd2.priority ASC, pd2.price ASC 
                LIMIT 1
              ) AS discount, 
							( SELECT price 
                FROM oc_product_special ps 
                WHERE 
                  ps.product_id = p1.id AND 
                  ps.customer_group_id = '1' AND 
                  ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND 
                  (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
                ORDER BY ps.priority ASC, ps.price ASC 
                LIMIT 1
              ) AS special 
						FROM 
							oc_product p1
						WHERE
							p1.status = '1'";
						
				if ($price_min & $price_max) {
					$sql .= " 
						HAVING (
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . " AND 
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
						)";
				} elseif ($price_min) {
					$sql .= " 
						HAVING (
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . "
						)";
				} elseif ($price_max) {
					$sql .= " 
						HAVING (
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
						)";
				}
				$sql .= " 	
					) AS p ON (pov.product_id = p.id)";

			} else {
				$sql .= "
					LEFT JOIN oc_product p ON (pov.product_id = p.id)"; 
			}			
				
			if ($data['filter_category_id']) {
        if (!empty($data['filter_sub_category'])) {
          $sql .= " 
              LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id) 
              LEFT JOIN oc_category_path cp ON (cp.category_id = p2c.category_id)"; 
        } else {
          $sql .= " LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id)";
        }
			}

      if (!empty($attributes)) {
        foreach ($attributes as $key => $attribute) {
          $sql .= " 
            LEFT JOIN oc_product_attribute pa" . ($key + 1) . " ON 
              (p.id = pa" . ($key + 1) . ".product_id AND pa" . ($key + 1) . ".language_id = '" . \Yii::$app->language . "') 
            LEFT JOIN oc_attribute a" . ($key + 1) . " ON 
              (pa" . ($key + 1) . ".attribute_id = a" . ($key + 1) . ".id)";
          }
      }
			if (!empty($options)) {
				foreach ($options as $key => $option) {
					if ($option != $sel_option['alias_name']) {
						$sql .= " 
              LEFT JOIN oc_product_option_value pov" . ($key + 1) . " 
                ON (p.id = pov" . ($key + 1) . ".product_id) 
              LEFT JOIN oc_option_value ov" . ($key + 1) . " ON 
                (ov" . ($key + 1) . ".id = pov" . ($key + 1) . ".option_value_id)";
					}
				}
			}
			if (!empty($filters)) {
				foreach ($filters as $key => $filter) {
					$sql .= " 
            LEFT JOIN oc_product_filter pf" . ($key + 1) . " ON 
              (p.id = pf" . ($key + 1) . ".product_id)
					  LEFT JOIN oc_filter f" . ($key + 1) . " ON 
              (pf" . ($key + 1) . ".filter_id = f" . ($key + 1) . ".id)
					  LEFT JOIN oc_filter_group fg" . ($key + 1) . " ON 
              (f" . ($key + 1) . ".filter_group_id = fg" . ($key + 1) . ".id)";
				}
			}

			if (!empty($colors)) {
				$sql .= " 	
					LEFT JOIN oc_pcolor pcl ON (pcl.id = p.pcolor_id)"; 
			}
		
			if (!empty($brands)) {
				$sql .= " LEFT JOIN oc_manufacturer m ON (p.manufacturer_id = m.id)";
			}
      if ($price_min || $price_max) {
        $sql .= ")";
      }
			
			$sql .= " WHERE ov.option_id = '" . (int)$sel_option['option_id'] . "' AND ovd.language_id = '" . \Yii::$app->language . "'";

      foreach ($attributes as $key => $attribute) {
        $sql .= " AND a" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($attribute);
        if (strpos($data[$attribute], ',')) {
          $arr_attribute = explode(',', $data[$attribute]);
          $str = Yii::$app->db->quoteValue(array_shift($arr_attribute));
          foreach ($arr_attribute as $item) {
              $str .= ", " . Yii::$app->db->quoteValue($item);
          }
          $sql .= " AND pa" . ($key + 1) . ".alias IN (" .  $str . ")";
        } elseif (strpos($data[$attribute], '[') === 0 && strpos($data[$attribute], '-') !== false) {
          $attr_temp = trim($data[$attribute], '[]');
          $arr_attribute = explode('-', $attr_temp);
          if ($arr_attribute[0] && $arr_attribute[1]) {
            $sql .= " AND (CAST(pa" . ($key + 1) . ".alias AS DECIMAL) BETWEEN " . (float)$arr_attribute[0] . " AND " . (float)$arr_attribute[1] . ")";
          } elseif ($arr_attribute[0]) {
            $sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) >= " . (float)$arr_attribute[0];
          } elseif ($arr_attribute[1]) {
            $sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) <= " . (float)$arr_attribute[1];
          }
        } else {
          $sql .= " AND pa" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$attribute]);
        }
      }
	
			foreach ($options as $key => $option) {
				if ($option != $sel_option['alias_name']) {
					if (strpos($data[$option], ',')) {
						$arr_option = explode(',', $data[$option]);
            $str = Yii::$app->db->quoteValue(array_shift($arr_option));
						foreach ($arr_option as $item) {
							$str .= ", " . Yii::$app->db->quoteValue($item);
						}
						$sql .= " AND ov" . ($key + 1) . ".alias IN (" . $str . ")";
					} else {
						$sql .= " AND ov" . ($key + 1) . ".alias = '" . Yii::$app->db->quoteValue($data[$option]) . "'";
					}
				}
			}

      foreach ($filters as $key => $filter) {
        $sql .= " AND fg" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($filter);
        if (strpos($data[$filter], ',')) {
          $arr_filter = explode(',', $data[$filter]);
          $str = Yii::$app->db->quoteValue(array_shift($arr_filter));
          foreach ($arr_filter as $item) {
              $str .= ", " . Yii::$app->db->quoteValue($item);
          }
          $sql .= " AND f" . ($key + 1) . ".alias IN (" .  $str . ")";
        } else {
          $sql .= " AND f" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$filter]);
        }
      }

      if (!empty($brands)) {
        if (strpos($data['brand'], ',')) {
          $brand_filter = explode(',', $data['brand']);
          $str = Yii::$app->db->quoteValue(array_shift($brand_filter));
          foreach ($brand_filter as $item) {
              $str .= ", " . Yii::$app->db->quoteValue($item);
          }
          $sql .= " AND m.alias IN (" .  $str . ")";
        } else {
          $sql .= " AND m.alias = " .  Yii::$app->db->quoteValue($data['brand']);
        }
      }

			if ($data['filter_category_id']) {
				if (!empty($data['filter_sub_category'])) {
					$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
				} else {
					$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				}
			}

			if (!empty($colors)) {
				if (strpos($data['color'], ',')) {
					$arr_color = explode(',', $data['color']);
          $str = Yii::$app->db->quoteValue(array_shift($arr_color));
					foreach ($arr_color as $item) {
						$str .= ", " . Yii::$app->db->quoteValue($item);
					}
					$sql .= " AND pcl.alias IN (" . $str . ")";
				} else {
					$str = "'" . $data['color'] . "'";
					$sql .= " AND pcl.alias = " . Yii::$app->db->quoteValue($data['color']);
				}
			}

			if (!$price_min && !$price_max) {
				$sql .= " AND p.status = '1'";
			}

			if (!(int)Yii::$app->shopConfig->getParam('show_zero_quantity')) {
				$sql .= " AND p.quantity > 0";
			}

			$sql .= " GROUP BY option_value_id";
			
			$sql .= " ORDER BY ov.sort_order ASC";

			$option_values2 = Yii::$app->db
        ->createCommand($sql)
        ->queryAll();

			foreach ($option_values2 as $result) {
				$option_data[] = [
          'filter_id'       => $result['option_value_id'],
					'opt_value' 	    => $result['opt_value'],
					'opt_eng_value'   => $result['alias_value'],
					'color_value'     => $result['color_value'],
					'quantity' 		    => $result['quantity'],
        ];
			}
			if ($option_data) {
				$results[] = [
          'filter_group_id'   => $sel_option['option_id'],
					'opt_name' 	 		    => $sel_option['opt_name'],
					'alias_name' 		    => $sel_option['alias_name'],
					'filter_sort_order' => $sel_option['filter_sort_order'],
					'open_filter' 		  => $sel_option['open_filter'],
					'option' 	 	 	      => $option_data,
        ];
			}
    }

    return $results;
  }

  public static function getCategoryFilters($data) {
    $filter_group_data = [];

    $colors = [];
    $attributes = [];
    $options = [];
    $filters = [];
    $brands = [];

    $all_attrs = Attribute::getAllAttributes();

    foreach ($all_attrs as $result) {
      if (!empty($data[$result['alias']])) {
        $attributes[] = $result['alias'];
      }
    }

    $all_opts = Option::getAllOptions();

    foreach ($all_opts as $result) {
      if (!empty($data[$result['alias']])) {
        $options[] = $result['alias'];
      }
    }

    $all_filters = FilterGroup::getAllFilters();

    foreach ($all_filters as $result) {
      if (!empty($data[$result['alias']])) {
        $filters[] = $result['alias'];
      }
    }

		if (!empty($data['color'])) {
			$colors[] = 'color';
		}

    if (!empty($data['brand'])) {
        $brands[] = 'brand';
    }

		$price_min = 0;
		$price_max = 0;
    if (!empty($data['filter_price'])) {
      $prices = explode('-', $data['filter_price']);
			$price_min = ((float)$prices[0] 
                      ? (float)Yii::$app->currency->convert((float)$prices[0], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
			$price_max = ((float)$prices[1] 
                      ? (float)Yii::$app->currency->convert((float)$prices[1], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
    }		
		
    $sql = "
			SELECT DISTINCT 
				fg.id, 
				fgd.name as name,
				fgd.description AS filter_desc,
				fg.alias as alias_name, 
				fg.filter_sort_order AS filter_sort_order,
				fg.open_filter AS open_filter,
				fg.icon AS icon,
				fg.sort_order 
			FROM oc_filter_group fg 
			LEFT JOIN oc_filter_group_description fgd ON (fg.id = fgd.filter_group_id)
			LEFT JOIN oc_filter f ON (fg.id = f.filter_group_id)
			LEFT JOIN oc_product_filter pf ON (f.id = pf.filter_id)
			LEFT JOIN oc_product p ON (pf.product_id = p.id)";

		if ($data['filter_category_id']) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " 
						LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id) 
						LEFT JOIN oc_category_path cp ON (cp.category_id = p2c.category_id)"; 
			} else {
        $sql .= " LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id)";
			}
		}
					
		$sql .= "
			WHERE 
				fgd.language_id = '" . \Yii::$app->language . "' AND
				p.status = '1'";
				
		if ($data['filter_category_id']) {
      if (!empty($data['filter_sub_category'])) {
        $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
      } else {
        $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
      }
		}

		if (!empty($data['filter_group_id'])) {
			$sql .= " AND fg.id = '" . (int)$data['filter_group_id'] . "'";
		}

		$sql .= "
			GROUP BY 
				fg.id";
			
		$sql .= "
			ORDER 
				BY fg.sort_order, LCASE(fgd.name)";

    $filter_groups = Yii::$app->db
      ->createCommand($sql)
      ->queryAll();

    foreach ($filter_groups as $filter_group) {
      $filter_data = [];

			$sql = "
      SELECT 
				f.id AS filter_id, 
				fd.name AS name, 
				f.alias AS alias_name,
				f.image as filter_image,
				COUNT(DISTINCT p.id) AS quantity";

			$sql .= "
				FROM oc_filter f 
				LEFT JOIN oc_filter_description fd ON (f.id = fd.filter_id)
				LEFT JOIN oc_product_filter pf ON (f.id = pf.filter_id)"; 

			if ($price_min || $price_max) {
			
				$sql .= " 
					LEFT JOIN ( 
						SELECT 
							p1.manufacturer_id AS manufacturer_id,
							p1.id AS id,
							p1.pcolor_id AS pcolor_id,
							p1.price AS price,
							p1.quantity AS quantity,
							( SELECT price 
                FROM oc_product_discount pd2 
                WHERE 
                  pd2.product_id = p1.id AND 
                  pd2.customer_group_id = '1' AND 
                  pd2.quantity = '1' AND 
                  ((pd2.date_start = 0 OR pd2.date_start < UNIX_TIMESTAMP(NOW())) AND 
                  (pd2.date_end = 0 OR pd2.date_end > UNIX_TIMESTAMP(NOW()))) 
                ORDER BY pd2.priority ASC, pd2.price ASC 
                LIMIT 1
              ) AS discount, 
							( SELECT price 
                FROM oc_product_special ps 
                WHERE 
                  ps.product_id = p1.id AND 
                  ps.customer_group_id = '1' AND 
                  ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND 
                  (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
                ORDER BY ps.priority ASC, ps.price ASC 
                LIMIT 1
              ) AS special 
						FROM 
							oc_product p1
						WHERE
							p1.status = '1'";
						
				if ($price_min & $price_max) {
					$sql .= " 
						HAVING (
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . " AND 
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
						)";
				} elseif ($price_min) {
					$sql .= " 
						HAVING (
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END >= " . $price_min . "
						)";
				} elseif ($price_max) {
					$sql .= " 
						HAVING (
							CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END <= " . $price_max . "
						)";
				}
				$sql .= " 	
					) AS p ON (pf.product_id = p.id)";

			} else {
				$sql .= "
					LEFT JOIN oc_product p ON (p.id = pf.product_id)";
			}			
				
			if ($data['filter_category_id']) {
        if (!empty($data['filter_sub_category'])) {
          $sql .= " 
              LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id) 
              LEFT JOIN oc_category_path cp ON (cp.category_id = p2c.category_id)"; 
        } else {
          $sql .= " LEFT JOIN oc_product_to_category p2c ON (p.id = p2c.product_id)";
        }
			}

      if (!empty($attributes)) {
        foreach ($attributes as $key => $attribute) {
          $sql .= " 
            LEFT JOIN oc_product_attribute pa" . ($key + 1) . " ON 
              (p.id = pa" . ($key + 1) . ".product_id AND pa" . ($key + 1) . ".language_id = '" . \Yii::$app->language . "') 
            LEFT JOIN oc_attribute a" . ($key + 1) . " ON 
              (pa" . ($key + 1) . ".attribute_id = a" . ($key + 1) . ".id)";
          }
      }
      if (!empty($options)) {
        foreach ($options as $key => $option) {
          $sql .= " 
            LEFT JOIN oc_product_option_value pov" . ($key + 1) . " ON 
              (p.id = pov" . ($key + 1) . ".product_id) 
            LEFT JOIN oc_option_value ov" . ($key + 1) . " ON 
              (ov" . ($key + 1) . ".id = pov" . ($key + 1) . ".option_value_id) 
            LEFT JOIN oc_option o" . ($key + 1) . " ON 
              (o" . ($key + 1) . ".id = ov" . ($key + 1) . ".option_id)";
        }
      }
			if (!empty($filters)) {
				foreach ($filters as $key => $filter) {
					if ($filter != $filter_group['alias_name']) {
						$sql .= " 
              LEFT JOIN oc_product_filter pf" . ($key + 1) . " ON 
                (p.id = pf" . ($key + 1) . ".product_id) 
						  LEFT JOIN oc_filter f" . ($key + 1) . " ON 
                (pf" . ($key + 1) . ".filter_id = f" . ($key + 1) . ".id)";
					}
				}
			}

			if (!empty($colors)) {
				$sql .= " 	
					LEFT JOIN oc_pcolor pcl ON (pcl.id = p.pcolor_id)"; 
			}
		
			if (!empty($brands)) {
				$sql .= " LEFT JOIN oc_manufacturer m ON (p.manufacturer_id = m.id)";
			}
      if ($price_min || $price_max) {
        $sql .= ")";
      }
			
			$sql .= " WHERE f.filter_group_id = '" . (int)$filter_group['id'] . "' AND fd.language_id = '" . \Yii::$app->language . "'";

      foreach ($attributes as $key => $attribute) {
        $sql .= " AND a" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($attribute);
        if (strpos($data[$attribute], ',')) {
          $arr_attribute = explode(',', $data[$attribute]);
          $str = Yii::$app->db->quoteValue(array_shift($arr_attribute));
          foreach ($arr_attribute as $item) {
              $str .= ", " . Yii::$app->db->quoteValue($item);
          }
          $sql .= " AND pa" . ($key + 1) . ".alias IN (" .  $str . ")";
        } elseif (strpos($data[$attribute], '[') === 0 && strpos($data[$attribute], '-') !== false) {
          $attr_temp = trim($data[$attribute], '[]');
          $arr_attribute = explode('-', $attr_temp);
          if ($arr_attribute[0] && $arr_attribute[1]) {
            $sql .= " AND (CAST(pa" . ($key + 1) . ".alias AS DECIMAL) BETWEEN " . (float)$arr_attribute[0] . " AND " . (float)$arr_attribute[1] . ")";
          } elseif ($arr_attribute[0]) {
            $sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) >= " . (float)$arr_attribute[0];
          } elseif ($arr_attribute[1]) {
            $sql .= " AND CAST(pa" . ($key + 1) . ".alias AS DECIMAL) <= " . (float)$arr_attribute[1];
          }
        } else {
          $sql .= " AND pa" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$attribute]);
        }
      }

      foreach ($options as $key => $option) {
        $sql .= " AND o" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($option);
        if (strpos($data[$option], ',')) {
          $arr_option = explode(',', $data[$option]);
          $str = Yii::$app->db->quoteValue(array_shift($arr_option));
          foreach ($arr_option as $item) {
              $str .= ", " . Yii::$app->db->quoteValue($item);
          }
          $sql .= " AND ov" . ($key + 1) . ".alias IN (" .  $str . ")";
        } else {
          $sql .= " AND ov" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$option]);
        }
      }

			foreach ($filters as $key => $filter) {
				if ($filter != $filter_group['alias_name']) {
					if (strpos($data[$filter], ',')) {
						$arr_filter = explode(',', $data[$filter]);
						$str = Yii::$app->db->quoteValue(array_shift($arr_filter));
						foreach ($arr_filter as $item) {
							$str .= ", " . Yii::$app->db->quoteValue($item);
						}
						$sql .= " AND f" . ($key + 1) . ".alias IN (" . $str . ")";
					} else {
						$sql .= " AND f" . ($key + 1) . ".alias = " .  Yii::$app->db->quoteValue($data[$filter]);
					}
				}
			}

      if (!empty($brands)) {
        if (strpos($data['brand'], ',')) {
          $brand_filter = explode(',', $data['brand']);
          $str = Yii::$app->db->quoteValue(array_shift($brand_filter));
          foreach ($brand_filter as $item) {
              $str .= ", " . Yii::$app->db->quoteValue($item);
          }
          $sql .= " AND m.alias IN (" .  $str . ")";
        } else {
          $sql .= " AND m.alias = " .  Yii::$app->db->quoteValue($data['brand']);
        }
      }

			if ($data['filter_category_id']) {
				if (!empty($data['filter_sub_category'])) {
					$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
				} else {
					$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				}
			}

			if (!empty($colors)) {
				if (strpos($data['color'], ',')) {
					$arr_color = explode(',', $data['color']);
          $str = Yii::$app->db->quoteValue(array_shift($arr_color));
					foreach ($arr_color as $item) {
						$str .= ", " . Yii::$app->db->quoteValue($item);
					}
					$sql .= " AND pcl.alias IN (" . $str . ")";
				} else {
					$str = "'" . $data['color'] . "'";
					$sql .= " AND pcl.alias = " . Yii::$app->db->quoteValue($data['color']);
				}
			}

			if (!$price_min && !$price_max) {
				$sql .= " AND p.status = '1'";
			}
			if (!(int)Yii::$app->shopConfig->getParam('show_zero_quantity')) {
				$sql .= " AND p.quantity > 0";
			}

			$sql .= " GROUP BY filter_id";
			
			$sql .= " ORDER BY f.sort_order, LCASE(fd.name)";

//			file_put_contents('1122-opts-'.$filter_group['alias_name'].'.txt', $sql);
			$filter_records = Yii::$app->db
        ->createCommand($sql)
        ->queryAll();

       // var_dump($sql);

      foreach ($filter_records as $filter) {
        $filter_data[] = [
          'filter_id' => $filter['filter_id'],
          'name'      => $filter['name'],
          'eng_name'  => $filter['alias_name'],
					'quantity'	=> $filter['quantity'],
					'image'		  => $filter['filter_image'],
        ];
      }

      if ($filter_data) {
        $filter_group_data[] = [
          'filter_group_id' 	=> $filter_group['id'],
          'name'            	=> $filter_group['name'],
          'eng_name'        	=> $filter_group['alias_name'],
          'filter_desc'       => $filter_group['filter_desc'],
          'filter_sort_order' => $filter_group['filter_sort_order'],
          'open_filter' 		  => $filter_group['open_filter'],
          'show_menu' 	 	    => $filter_group['show_menu'],
          'icon' 				      => $filter_group['icon'],
          'filter'          	=> $filter_data
        ];
      }
    }

    return $filter_group_data;
  }

  private static function _getFieldsData(array $filter_params)
  {

    self::$filter_count = 0;
    self::$saved_filters = [];

    self::$brand_name = Translation::getTranslation('Brand');
    self::$color_name = Translation::getTranslation('Color');
		
    if ((int)Yii::$app->shopConfig->getParam('filter_show_attributes')) {
			$results = Attribute::getAllAttributes();
			foreach ($results as $result) {
				if (!empty($filter_params[$result['alias']])) {
					self::$get_attributes[] = $result['alias'];
					self::$filter_count++;
					
					if ($result['value_type'] == Attribute::VALUE_TYPE_CHECKBOX || $result['value_type'] == Attribute::VALUE_TYPE_RADIO) { // если это чекбокс или радио
						$sel_names = Attribute::getSelectedAttributes($result['alias'], explode(',', $filter_params[$result['alias']]));
						$names = [];
						foreach ($sel_names as $alias => $name) {
              $names[] = [
                'alias' => $alias,
                'name' => $name
              ];
						}
					} elseif ($result['value_type'] == 1) { // если это диапазон
						$sel_values = trim($filter_params[$result['alias']], '[]');
						$sel_values = explode('-', $sel_values);
						$names = array(
							'min_value' => $sel_values[0],
							'max_value' => $sel_values[1],
						);
					}

					self::$saved_filters[] = [
            'value_type' => (int)$result['value_type'],
            'group' => 'attribute',
						'alias' => $result['alias'],
            'icon' => $result['icon'],
						'name' => $result['name'],
						'selected' => $names,
          ];
				}
			}
		}

		if ((int)Yii::$app->shopConfig->getParam('filter_show_options')) {
			$results = Option::getAllOptions();
			foreach ($results as $result) {
				if (!empty($filter_params[$result['alias']])) {
					self::$get_options[] = $result['alias'];
					self::$filter_count++;

					$sel_names = Option::getSelectedOptions($result['alias'], explode(',', $filter_params[$result['alias']]));
					$names = [];
					foreach ($sel_names as $alias => $name) {
            $names[] = [
              'alias' => $alias,
              'name' => $name
            ];
					}

					self::$saved_filters[] = [
            'value_type' => 0,
            'group' => 'option',
						'alias' => $result['alias'],
						'name' => $result['name'],
						'selected' => $names,
          ];
				}
			}
		}

		if ((int)Yii::$app->shopConfig->getParam('filter_show_filters')) {
			$results = FilterGroup::getAllFilters();
			foreach ($results as $result) {
				if (!empty($filter_params[$result['alias']])) {
					self::$get_filters[] = $result['alias'];
					self::$filter_count++;

					$sel_names = Filter::getSelectedFilters($result['alias'], explode(',', $filter_params[$result['alias']]));
					$names = [];
					foreach ($sel_names as $alias => $name) {
            $names[] = [
              'alias' => $alias,
              'name' => $name
            ];
					}

					self::$saved_filters[] = [
            'value_type' => 0,
            'group' => 'filter',
						'alias' => $result['alias'],
						'name' => $result['name'],
            'icon' => $result['icon'],
						'selected' => $names,
					];
				}
			}
		}

		if ((int)Yii::$app->shopConfig->getParam('filter_show_brands')) {
			if (!empty($filter_params['brand'])) {
				self::$get_brands[] = 'brand';
				$brands = explode(',', $filter_params['brand']);
				self::$filter_count += count($brands);

				$sel_names = Manufacturer::getSelectedBrands($brands);
				$names = [];
				foreach ($sel_names as $alias => $name) {
					$names[] = [
            'alias' => $alias,
            'name' => $name
          ];
				}

				self::$saved_filters[] = [
          'value_type' => 0,
					'group' => 'brand',
					'alias' => 'brand',
					'name' => self::$brand_name,
          'icon' => '/upload/image/icons/brand.svg',
					'selected' => $names,
        ];
			}
		}

		if ((int)Yii::$app->shopConfig->getParam('filter_show_colors')) {
      if (!empty($filter_params['color'])) {
        self::$get_colors[] = 'color';
        $colors = explode(',', $filter_params['color']);
        self::$filter_count += count($colors);

        if (!isset($saved_filters['color'])) {
          $saved_filters['color'] = array();
        }
        $sel_names = Pcolor::getSelectedColors($colors);
        $names = array();
        foreach ($sel_names as $alias => $color_item) {
					$names[] = [
            'alias' => $alias,
            'name' => $color_item['name'],
            'icon' => $color_item['icon'],
          ];
        }

        self::$saved_filters[] = array(
          'value_type' => 0,
					'group' => 'color',
          'alias' => 'color',
          'name' => self::$color_name,
          'selected' => $names,
        );
      }
		}

    if (!empty($filter_params['price'])) {
      self::$get_price[] = 'price';
			self::$filter_count++;
    }
  }

  public static function getFilterData(int $category_id, array $filter_params)
  {

		if (is_null(self::$filter_count)) {
      self::_getFieldsData($filter_params);
    }
		
		$data['saved_filters'] = self::$saved_filters;

		$data['prices_out'] = false;
		$data['filter_groups'] = [];
		
		if ((int)Yii::$app->shopConfig->getParam('filter_show_price')) {

			if (isset($filter_params['price']) && $filter_params['price']) {
				$minmax_prices = explode('-', $filter_params['price']);
				$data['min_price'] = isset($minmax_prices[0]) ? $minmax_prices[0] : '';
				$data['max_price'] = isset($minmax_prices[1]) ? $minmax_prices[1] : '';
			} else {
				$data['min_price'] = '';
				$data['max_price'] = '';
			}

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_sub_category' => true,
			);

			foreach (self::$get_attributes as $get_attribute) {
				$filter_data[$get_attribute] = $filter_params[$get_attribute];
			}
			foreach (self::$get_options as $get_option) {
				$filter_data[$get_option] = $filter_params[$get_option];
			}
			foreach (self::$get_filters as $get_filter) {
				$filter_data[$get_filter] = $filter_params[$get_filter];
			}
			foreach (self::$get_brands as $get_brand) {
				$filter_data[$get_brand] = $filter_params[$get_brand];
			}
			foreach (self::$get_colors as $get_color) {
				$filter_data[$get_color] = $filter_params[$get_color];
			}

			$original_minmax_prices = Product::getProductsMinMaxPrices($filter_data);
			$data['original_min_price'] = isset($original_minmax_prices['min_price']) 
          ? Yii::$app->currency->format($original_minmax_prices['min_price'], Yii::$app->currency->getCurrent(), '', false) 
          : '';
			$data['original_max_price'] = isset($original_minmax_prices['max_price']) 
          ? Yii::$app->currency->format($original_minmax_prices['max_price'], Yii::$app->currency->getCurrent(), '', false) 
          : '';

			$data['decimal_place'] = Yii::$app->currency->getDecimalPlace(Yii::$app->currency->getCurrent());

			if (!$original_minmax_prices['min_price'] && !$original_minmax_prices['max_price'] || (float)$original_minmax_prices['min_price'] == (float)$original_minmax_prices['max_price']){
				$data['prices_out'] = false;
			}
		}

		$filter_data = array(
			'filter_category_id'  => $category_id, 
			'filter_sub_category' => true,
		);
		foreach (self::$get_attributes as $get_attribute) {
			$filter_data[$get_attribute] = $filter_params[$get_attribute];
		}
		foreach (self::$get_options as $get_option) {
			$filter_data[$get_option] = $filter_params[$get_option];
		}
		foreach (self::$get_filters as $get_filter) {
			$filter_data[$get_filter] = $filter_params[$get_filter];
		}
		foreach (self::$get_brands as $get_brand) {
			$filter_data[$get_brand] = $filter_params[$get_brand];
		}
    foreach (self::$get_colors as $get_color) {
      $filter_data[$get_color] = $filter_params[$get_color];
    }
		foreach (self::$get_price as $prices) {
			$filter_data['filter_'.$prices] = $filter_params[$prices];
		}

    $f_manufacturers = false;
		if ((int)Yii::$app->shopConfig->getParam('filter_show_brands')) {
			$f_manufacturers = self::getCategoryManufacturers($filter_data, self::$brand_name);
		}
		
    $f_colors = false;
		if ((int)Yii::$app->shopConfig->getParam('filter_show_colors')) {
			$f_colors = self::getCategoryColors($filter_data, self::$color_name);
		}
		
    $f_attributes = false;
		if ((int)Yii::$app->shopConfig->getParam('filter_show_attributes')) {
			$f_attributes = self::getCategoryAttributes($filter_data);
		}

    $f_options = false;
    if ((int)Yii::$app->shopConfig->getParam('filter_show_options')) {
			$f_options = self::getCategoryOptions($filter_data);
		}

    $filter_groups = false;
    if ((int)Yii::$app->shopConfig->getParam('filter_show_filters')) {
			$filter_groups = self::getCategoryFilters($filter_data);
		}

		$brands_open = (bool)Yii::$app->shopConfig->getParam('filter_brands_open');
		$brands_sort = (int)Yii::$app->shopConfig->getParam('filter_brands_sort_order');

		if ($f_manufacturers) {
			foreach ($f_manufacturers as $key_name => $manufacturer) {
				$key_name = explode(';', $key_name);
				if (isset($filter_params[$key_name[1]])) {
					$get_brand_vals = explode(',', $filter_params[$key_name[1]]);
				} else {
					$get_brand_vals = [];
				}
				$childen_data = [];

				$is_open = $brands_open;
				foreach ($manufacturer as $manufacturer_value) {

					if (in_array($key_name[1], self::$get_brands) && in_array($manufacturer_value['brand_eng_value'], $get_brand_vals)) {
						$is_selected = true;
						$is_open = true;
					} else {
						$is_selected = false;
					}

					$childen_data[] = array(
						'filter_id'   => $manufacturer_value['filter_id'],
						'eng_name'    => $manufacturer_value['brand_eng_value'],
						'name'        => $manufacturer_value['brand_value'],
						'is_color'	  => 0,
						'is_option'	  => 0,
						'quantity'	  => $manufacturer_value['quantity'],
						'is_selected' => $is_selected,
					);
				}

				if (count($childen_data) > 1 || !empty(self::$get_brands)) {
					$data['filter_groups'][$brands_sort] = array(
						'filter_group_id' => 'brand',
						'type'			      => 'brand',
						'name'			      => $key_name[0],
						'description'	    => '',
						'eng_name'        => $key_name[1],
						'is_open'		      => $is_open,
						'value_type'	    => 0,
						'icon'	  		    => '/upload/image/icons/brand.svg',
						'filter'          => $childen_data
					);
				}
			}
		}

		$colors_open = (bool)Yii::$app->shopConfig->getParam('filter_colors_open');
		$colors_sort = (int)Yii::$app->shopConfig->getParam('filter_colors_sort_order');

		if ($f_colors) {
			foreach ($f_colors as $key_name => $color) {
				$key_name = explode(';', $key_name);
				if (isset($filter_params[$key_name[1]])) {
					$get_color_vals = explode(',', $filter_params[$key_name[1]]);
				} else {
					$get_color_vals = [];
				}
				$childen_data = [];

				$is_open = $colors_open;
				foreach ($color as $color_value) {

					if (in_array($key_name[1], self::$get_colors) && in_array($color_value['color_eng_value'], $get_color_vals)) {
						$is_selected = true;
						$is_open = true;
					} else {
						$is_selected = false;
					}

					$childen_data[] = [
						'filter_id'     => $color_value['filter_id'],
						'eng_name'      => $color_value['color_eng_value'],
						'name'          => $color_value['color_value'],
						'is_color'	    => 1,
						'is_option'	    => 0,
						'quantity'	    => $color_value['quantity'],
						'icon'		      => $color_value['color_image'],
						'is_selected'   => $is_selected,
          ];
				}

				if (count($childen_data) > 1 || !empty($get_colors)) {
					$data['filter_groups'][(int)$colors_sort] = [
						'filter_group_id' => 'color',
						'type'			      => 'color',
						'name'			      => $key_name[0],
						'description'	    => '',
						'eng_name'        => $key_name[1],
						'is_open'		      => $is_open,
						'value_type'	    => 0,
						'icon'	  		    => '',
						'filter'          => $childen_data
          ];
				}
			}
		}

    if ($f_options) {
			foreach ($f_options as $option) {
				$alias_name = $option['alias_name'];
				$opt_name = $option['opt_name'];
				if (isset($filter_params[$alias_name])) {
					$get_opt_vals = explode(',', $filter_params[$alias_name]);
				} else {
					$get_opt_vals = [];
				}
				$childen_data = [];

				$is_open = (bool)$option['open_filter'];
				foreach ($option['option'] as $option_value) {

					if (in_array($alias_name, self::$get_options) && in_array($option_value['opt_eng_value'], $get_opt_vals)) {
						$is_selected = true;
						$is_open = true;
					} else {
						$is_selected = false;
					}

					$childen_data[] = array(
						'filter_id'   => $option_value['filter_id'],
						'eng_name'    => $option_value['opt_eng_value'],
						'name'        => $option_value['opt_value'],
						'is_color'	  => (int)($option['alias_name'] == 'color'),
						'color'		    => $option_value['color_value'],
						'is_option'	  => 1,
						'quantity'	  => $option_value['quantity'],
						'is_selected' => $is_selected,
					);
				}

				if (count($childen_data) > 1 || in_array($alias_name, self::$get_options)) {
					$data['filter_groups'][(int)$option['filter_sort_order']] = array(
						'filter_group_id' => $option['filter_group_id'],
						'type'			      => 'option',
						'name'			      => $opt_name,
						'description'	    => '',
						'eng_name'        => $alias_name,
						'is_open'		      => $is_open,
						'value_type'	    => 0,
						'icon'	  		    => '',
						'filter'          => $childen_data
					);
				}
			}
		}
		if ($f_attributes) {
			foreach ($f_attributes as $attribute) {
				$key_name = $attribute['alias_name'];
				$attr_name = $attribute['attr_name'];
				$attr_icon = $attribute['icon'];
				$attr_desc = str_replace('src="/upload', 'src="//next-cart.site/upload', html_entity_decode($attribute['attr_desc'], ENT_QUOTES, 'UTF-8'));
				if (isset($filter_params[$key_name])) {
					if ($attribute['value_type'] == 0 || $attribute['value_type'] == 2) {
						$get_attr_vals = explode(',', $filter_params[$key_name]);
					} elseif ($attribute['value_type'] == 1) {
						$key_name_temp = trim($filter_params[$key_name], '[]');
						$get_attr_vals = explode('-', $key_name_temp);
						$sel_min_value = $get_attr_vals[0];
						$sel_max_value = $get_attr_vals[1];
					}
				} else {
					if ($attribute['value_type'] == 0) {
						$get_attr_vals = [];
					} elseif ($attribute['value_type'] == 1) {
						$sel_min_value = '';
						$sel_max_value = '';
					}
				}
				$childen_data = [];

				$is_open = (bool)$attribute['open_filter'];
				if ($attribute['value_type'] == Attribute::VALUE_TYPE_CHECKBOX || $attribute['value_type'] == Attribute::VALUE_TYPE_RADIO) { // если это чекбокс или радио
					foreach ($attribute['attribute'] as $attribute_value) {

						if (in_array($key_name, self::$get_attributes) && in_array($attribute_value['attr_eng_value'], $get_attr_vals)) {
							$is_selected = true;
							$is_open = true;
						} else {
							$is_selected = false;
						}
						
						$childen_data[] = array(
							'filter_id'   => $attribute_value['filter_id'],
							'eng_name'    => $attribute_value['attr_eng_value'],
							'name'        => $attribute_value['attr_value'],
							'is_color'	  => 0,
							'is_option'	  => 0,
							'quantity'	  => $attribute_value['quantity'],
							'is_selected' => $is_selected,
						);
					}

					if (count($childen_data) > 1 || in_array($key_name, self::$get_attributes)) {
						$data['filter_groups'][(int)$attribute['filter_sort_order']] = array(
							'filter_group_id' => $attribute['filter_group_id'],
							'type'			      => 'attribute',
							'name'			      => $attr_name,
							'description'	    => $attr_desc,
							'eng_name'        => $key_name,
							'is_open'		      => $is_open,
							'value_type'	    => (int)$attribute['value_type'],
							'icon'	  		    => $attr_icon,
							'filter'          => $childen_data
						);
					}

				} elseif ($attribute['value_type'] == Attribute::VALUE_TYPE_RANGE) { // если это диапазон
					$childen_data = $attribute['attribute'];
					$childen_data['sel_min_value'] = (float)$sel_min_value ? $sel_min_value : '';
					$childen_data['sel_max_value'] = (float)$sel_max_value ? $sel_max_value : '';
					
					$is_open = $is_open || !empty($childen_data['sel_min_value']) || !empty($childen_data['sel_max_value']);

					if ($childen_data['min_value'] < $childen_data['max_value'] && $childen_data['min_value'] != 3999999999 && $childen_data['max_value'] != -3999999999 ) {
						$data['filter_groups'][(int)$attribute['filter_sort_order']] = array(
							'filter_group_id' => $attribute['filter_group_id'],
							'type'			      => 'attribute',
							'name'			      => $attr_name,
							'description'	    => $attr_desc,
							'eng_name'        => $key_name,
							'is_open'		      => $is_open,
							'value_type'	    => $attribute['value_type'],
							'icon'	  		    => $attr_icon,
							'filter'          => $childen_data
						);
					}
				}
			}
		}

		if ($filter_groups) {
			foreach ($filter_groups as $filter_group) {
				
				$filter_icon = $filter_group['icon'];
				$filter_desc = html_entity_decode($filter_group['filter_desc'], ENT_QUOTES, 'UTF-8');
				
				if (isset($filter_params[$filter_group['eng_name']])) {
					$get_filter_vals = explode(',', $filter_params[$filter_group['eng_name']]);
				} else {
					$get_filter_vals = [];
				}
				$childen_data = [];

				$is_open = (bool)$filter_group['open_filter'];
				foreach ($filter_group['filter'] as $filter) {

					if (in_array($filter_group['eng_name'], self::$get_filters) && in_array($filter['eng_name'], $get_filter_vals)) {
						$is_selected = true;
						$is_open = true;
					} else {
						$is_selected = false;
					}

					$childen_data[] = array(
						'filter_id'   => $filter['filter_id'],
						'eng_name'    => $filter['eng_name'],
						'name'        => $filter['name'],
						'is_color'	  => 0,
						'is_option'	  => 0,
						'quantity'  	=> $filter['quantity'],
						'icon'	    	=> $filter['image'],
						'is_selected' => $is_selected,
					);
				}

				if (count($childen_data) > 1 || in_array($filter_group['eng_name'], self::$get_filters)) {
					$data['filter_groups'][(int)$filter_group['filter_sort_order']] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'type'			      => 'filter',
						'name'            => $filter_group['name'],
						'description'	    => $filter_desc,
						'eng_name'        => $filter_group['eng_name'],
						'is_open'		      => $is_open,
						'value_type'	    => 0,
						'icon'	  		    => $filter_icon,
						'filter'          => $childen_data
					);
				}
			}
		}
		
		ksort($data['filter_groups']);
    $data['filter_groups'] = array_values($data['filter_groups']);

    return $data;
  }
}
