<?php

namespace frontend\models;

use common\models\Product as ModelsProduct;
use himiklab\thumbnail\EasyThumbnailImage;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "trip".
 */
class Product extends ModelsProduct
{

  public $name;
  public $short_name;
  
  public static function getMainCategoryId(int $product_id)
  {
    return (int)ProductToCategory::find()
      ->select('category_id')
      ->where(['product_id' => $product_id, 'main_category' => '1'])
      ->limit(1)
      ->scalar();
  }

  public function fetchMainCategoryId()
  {
    return (int)ProductToCategory::find()
      ->select('category_id')
      ->where(['product_id' => $this->id, 'main_category' => '1'])
      ->limit(1)
      ->scalar();
  }

  public static function getBreadcrumbs($product)
  {
    $breadcrumbs = [
      [
        'title' => Translation::getTranslation('Catalog'),
        'href' => '/catalog',
      ],
    ];

    $category = Category::getCategory(self::getMainCategoryId($product['id']));
    $href = ['catalog'];

    $breadcrumbs_rev = [];
    while ($category) {
      $breadcrumbs_rev[] = [
        'title' => $category['name'],
        'alias' => $category['alias'],
      ];
      $category = Category::getCategory((int)$category['parent_id']);
    }

    foreach (array_reverse($breadcrumbs_rev) as $item) {
      $href[] = $item['alias'];
      $breadcrumbs[] = [
        'title' => $item['title'],
        'href' => '/'. implode('/', $href),
      ];
    }

    $breadcrumbs[] = [
      'title' => $product['name'],
      'href' => '',
    ];

    return $breadcrumbs;
  }

  public static function getProduct($id, $image_type = 'thumbs_catalog')
  {
    return self::_getProduct(['id' => (int)$id, 'image_type' => $image_type]);
  }

  public static function getProductBySlug($slug, $image_type = 'thumbs_catalog')
  {
    return self::_getProduct(['alias' => $slug, 'image_type' => $image_type]);
  }

  private static function _getProduct($param)
  {
		$sql = "
      SELECT DISTINCT 
        p.*, pd.*, 
        ( SELECT md.name 
          FROM oc_manufacturer_description md 
          WHERE 
            md.manufacturer_id = p.manufacturer_id AND 
            md.language_id = '" . \Yii::$app->language . "'
        ) AS manufacturer, 
        ( SELECT pcld.name 
          FROM oc_pcolor_description pcld 
          WHERE 
            pcld.pcolor_id = p.pcolor_id AND 
            pcld.language_id = '" . \Yii::$app->language . "'
        ) AS color_name, 
        ( SELECT pcl.image 
          FROM oc_pcolor pcl
          WHERE 
            pcl.id = p.pcolor_id
        ) AS color_image, 
        ( SELECT price 
          FROM oc_product_discount pd2 
          WHERE 
            pd2.product_id = p.id AND 
            pd2.customer_group_id = '" . (int)Yii::$app->shopConfig->getParam('customer_group_id') . "' AND 
            pd2.quantity = '1' AND 
            ((pd2.date_start = 0 OR pd2.date_start < UNIX_TIMESTAMP(NOW())) AND 
            (pd2.date_end = 0 OR pd2.date_end > UNIX_TIMESTAMP(NOW()))) 
          ORDER BY pd2.priority ASC, pd2.price ASC 
          LIMIT 1
        ) AS discount, 
        ( SELECT price 
          FROM oc_product_special ps 
          WHERE 
            ps.product_id = p.id AND 
            ps.customer_group_id = '" . (int)Yii::$app->shopConfig->getParam('customer_group_id') . "' AND 
            ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND 
            (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
          ORDER BY ps.priority ASC, ps.price ASC 
          LIMIT 1
        ) AS special, 
        ( SELECT ssd.name 
          FROM oc_stock_status_description ssd
          WHERE 
            ssd.stock_status_id = p.stock_status_id AND 
            ssd.language_id = '" . \Yii::$app->language . "'
        ) AS stock_status, 
        ( SELECT wcd.unit 
          FROM oc_weight_class_description wcd 
          WHERE 
            p.weight_class_id = wcd.weight_class_id AND 
            wcd.language_id = '" . \Yii::$app->language . "'
        ) AS weight_class, 
        ( SELECT lcd.unit 
          FROM oc_length_class_description lcd 
          WHERE 
            p.length_class_id = lcd.length_class_id AND 
            lcd.language_id = '" . \Yii::$app->language . "'
        ) AS length_class, 
        ( SELECT AVG(rating) AS total 
          FROM oc_review r1 
          WHERE 
            r1.product_id = p.id AND 
            r1.status = '1' 
          GROUP BY r1.product_id
        ) AS rating, 
        ( SELECT COUNT(*) AS total 
          FROM oc_review r2 
          WHERE 
            r2.product_id = p.id AND 
            r2.status = '1' 
          GROUP BY r2.product_id
        ) AS reviews, 
        p.sort_order, 
        ( SELECT COUNT(pov.id) 
          FROM oc_product_option_value pov 
          WHERE p.id = pov.product_id
        ) AS options_count 
        
      FROM oc_product p 
      LEFT JOIN oc_product_description pd ON (p.id = pd.product_id) 
      
      WHERE 
        pd.language_id = '" . \Yii::$app->language . "' AND 
        p.status = '1' AND 
        p.date_available <= UNIX_TIMESTAMP(NOW()) AND";

    if (!empty($param['id'])) {
      $sql .= " p.id = '" . (int)$param['id'] . "'";
    } elseif (!empty($param['alias'])) {
      $sql .= " p.alias = " . Yii::$app->db->quoteValue($param['alias']);
    } else {
      return false;
    }

    $product = Yii::$app->db
      ->createCommand($sql)
      ->queryOne();

		if ($product) {

      $thumb = EasyThumbnailImage::thumbnailFileUrl(
        '@root' . ($product['image'] ? $product['image'] : '/upload/img/banners/no_image.png'),
        (int)Yii::$app->shopConfig->getParam($param['image_type'] . '_width'),
        (int)Yii::$app->shopConfig->getParam($param['image_type'] . '_height'),
        EasyThumbnailImage::THUMBNAIL_INSET
      );

			return [
				'id'               => (int)$product['id'],
				'alias'            => $product['alias'],
				'name'             => $product['name'],
				'short_name'       => $product['short_name'],
				'description'      => $product['description'],
				'meta_title'       => $product['meta_title'],
				'meta_h1'          => $product['meta_h1'],
				'meta_description' => $product['meta_description'],
				'meta_keyword'     => $product['meta_keyword'],
				'tag'              => $product['tag'],
				'model'            => $product['model'],
				'sku'              => $product['sku'],
				'upc'              => $product['upc'],
				'ean'              => $product['ean'],
				'jan'              => $product['jan'],
				'isbn'             => $product['isbn'],
				'mpn'              => $product['mpn'],
				'location'         => $product['location'],
				'quantity'         => $product['quantity'],
				'stock_status'     => $product['stock_status'],
				'stock_status_id'  => (int)$product['stock_status_id'],
				'image'            => $product['image'],
				'thumb'            => $thumb,
				'manufacturer_id'  => (int)$product['manufacturer_id'],
				'manufacturer'     => $product['manufacturer'],
				'price'            => (float)($product['discount'] ? $product['discount'] : $product['price']),
				'special'          => (float)$product['special'],
				'color_name'       => $product['color_name'],
				'color_image'      => $product['color_image'],
				'points'           => (int)$product['points'],
				'date_available'   => (int)$product['date_available'],
				'weight'           => (float)$product['weight'],
				'weight_class_id'  => (int)$product['weight_class_id'],
				'length'           => (float)$product['length'],
				'width'            => (float)$product['width'],
				'height'           => (float)$product['height'],
				'length_class_id'  => (int)$product['length_class_id'],
				'subtract'         => (int)$product['subtract'],
				'rating'           => round((float)$product['rating'], 1),
				'reviews'          => (int)$product['reviews'] ? (int)$product['reviews'] : 0,
				'minimum'          => (int)$product['minimum'],
				'sort_order'       => (int)$product['sort_order'],
				'status'           => (int)$product['status'],
				'created_at'       => (int)$product['created_at'],
				'updated_at'       => (int)$product['updated_at'],
				'viewed'           => (int)$product['viewed'],
				'options_count'    => (int)$product['options_count'],
      ];
		} else {
			return false;
		}
  }

  public static function fillProduct(array &$product, bool $tile = false)
  {
    if ($tile) {
      unset($product['description']);
    } else {
      $product['description'] = str_replace('src="/upload', 'src="//next-cart.site/upload', html_entity_decode($product['description']));
    }

    $product['price_str'] = Yii::$app->currency->format((float)$product['price'], Yii::$app->currency->getCurrent());
    if ((float)$product['special']) {
      $product['special_str'] = Yii::$app->currency->format((float)$product['special'], Yii::$app->currency->getCurrent());
      $product['special_percent'] = number_format(round(((float)$product['price'] - (float)$product['special'])/(float)$product['price'] * 100), 0, '.', '');
    } else {
      $product['special_str'] = '';
      $product['special_percent'] = '';
    }
    $product['attributes'] = ProductAttribute::getProductAttributes($product['id']);
    $product['options'] = ProductOption::getProductOptions($product['id']);
    $product['filters'] = ProductFilter::getProductFilters($product['id']);
    $product['images'] = ProductImage::getProductImages($product['id'], $tile ? 'thumbs_catalog' : 'thumbs_product');
    if ($tile) {
      array_unshift($product['images'], [
        'id' => '0-' . $product['id'],
        'image' => $product['image'],
        'thumb' => $product['thumb'],
      ]);
    } else {
      array_unshift($product['images'], [
        'id' => '0-' . $product['id'],
        'image' => $product['image'],
        'thumb_big' => EasyThumbnailImage::thumbnailFileUrl(
          '@root' . ($product['image'] ? $product['image'] : '/upload/img/banners/no_image.png'),
          (int)Yii::$app->shopConfig->getParam('thumbs_product_big_width'),
          (int)Yii::$app->shopConfig->getParam('thumbs_product_big_height'),
          EasyThumbnailImage::THUMBNAIL_INSET_BOX
        ),
        'thumb_gallery' => EasyThumbnailImage::thumbnailFileUrl(
          '@root' . ($product['image'] ? $product['image'] : '/upload/img/banners/no_image.png'),
          (int)Yii::$app->shopConfig->getParam('thumbs_product_gallery_width'),
          (int)Yii::$app->shopConfig->getParam('thumbs_product_gallery_height'),
          EasyThumbnailImage::THUMBNAIL_INSET_BOX
        ),
      ]);
    }
    $product['image_item'] = 0;
  }

  public static function getProducts(array $data = [], string $image_type = 'thumbs_catalog')
  {

    $attributes = [];
    $options = [];
    $filters = [];
    $brands = [];
    $colors = [];

    $attribute_aliases = Attribute::getAliases();
    $options_aliases = Option::getAliases();
    $filters_aliases = FilterGroup::getAliases();

    foreach ($attribute_aliases as $alias) {
      if (!empty($data[$alias])) $attributes[] = $alias;
    }
    foreach ($options_aliases as $alias) {
      if (!empty($data[$alias])) $options[] = $alias;
    }
    foreach ($filters_aliases as $alias) {
      if (!empty($data[$alias])) $filters[] = $alias;
    }
    if (!empty($data['brand'])) $brands[] = 'brand';
    if (!empty($data['color'])) $colors[] = 'color';
    
    // Yii::$app->shopConfig->getParam('email')
		$sql = " 
      SELECT 
        p.id, 
        (SELECT AVG(rating) 
          FROM oc_review r1 
          WHERE r1.product_id = p.id AND r1.status = '1' 
          GROUP BY r1.product_id
        ) AS rating, 
        (SELECT price 
          FROM oc_product_discount pd2 
          WHERE 
            pd2.product_id = p.id AND 
            pd2.customer_group_id = '" . Yii::$app->shopConfig->getParam('customer_group_id') . "' AND 
            pd2.quantity = '1' AND 
            ((pd2.date_start = 0 OR pd2.date_start < UNIX_TIMESTAMP(NOW())) AND 
            (pd2.date_end = 0 OR pd2.date_end > UNIX_TIMESTAMP(NOW()))) 
          ORDER BY pd2.priority ASC, pd2.price ASC 
          LIMIT 1
        ) AS discount, 
        (SELECT price 
          FROM oc_product_special ps 
          WHERE 
            ps.product_id = p.id AND 
            ps.customer_group_id = '" . Yii::$app->shopConfig->getParam('customer_group_id') . "' AND 
            ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND 
            (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
          ORDER BY ps.priority ASC, ps.price ASC 
          LIMIT 1
        ) AS special";

		if (!empty($data['filter_category_id'])) {
      if (!empty($data['filter_sub_category'])) {
        $sql .= " FROM oc_category_path cp LEFT JOIN oc_product_to_category p2c ON (cp.category_id = p2c.category_id)";
      } else {
        $sql .= " FROM oc_product_to_category p2c ON (cp.category_id = p2c.category_id)";
      }
      $sql .= " LEFT JOIN oc_product p ON (p2c.product_id = p.id)";
		} else {
			$sql .= " FROM oc_product p";
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
            LEFT JOIN oc_filter f" . ($key + 1) . " ON 
              (pf" . ($key + 1) . ".filter_id = f" . ($key + 1) . ".id) 
            LEFT JOIN oc_filter_group fg" . ($key + 1) . " ON 
              (f" . ($key + 1) . ".filter_group_id = fg" . ($key + 1) . ".id)";
        }
    }

    if (!empty($colors)) {
        $sql .= " LEFT JOIN oc_pcolor pcl ON (pcl.id = p.pcolor_id)";
    }

    if (!empty($brands)) {
        $sql .= "
          LEFT JOIN oc_manufacturer m ON (p.manufacturer_id = m.id)";
    }

    $sql .= "
      LEFT JOIN oc_product_description pd ON (p.id = pd.product_id) 
      WHERE 
        pd.language_id = '" . \Yii::$app->language . "' AND 
        p.status = '1' AND 
        p.date_available <= UNIX_TIMESTAMP(NOW())";


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

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE " . Yii::$app->db->quoteValue("%" . $word. "%");
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				$sql .= " OR pd.meta_keyword LIKE " . Yii::$app->db->quoteValue("%" . $data['filter_name'] . "%");

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE " . Yii::$app->db->quoteValue("%" . $data['filter_name'] . "%");
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE " . Yii::$app->db->quoteValue("%" . $word . "%");
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
        
				$sql .= " OR LCASE(p.model) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.sku) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.upc) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.ean) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.jan) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.isbn) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.mpn) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
        
      }

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		if (!empty($data['filter_exclude'])) {
			$sql .= " AND p.id <> '" . (int)$data['filter_exclude'] . "'";
		}
		
		if (!Yii::$app->shopConfig->getParam('show_zero_quantity')) {
			$sql .= " AND p.quantity > 0";
		}

		$sql .= " GROUP BY p.id";

    if (!empty($data['filter_price'])) {
      $prices = explode('-', $data['filter_price']);
      $price_min = ((float)$prices[0] 
                      ? (float)Yii::$app->currency->convert((float)$prices[0], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);
      $price_max = ((float)$prices[1] 
                      ? (float)Yii::$app->currency->convert((float)$prices[1], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency')) 
                      : 0);

      if ($price_min & $price_max) {
        $sql .= "
          HAVING (
            MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END) >= " . $price_min . " AND 
            MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END) <= " . $price_max . "
          )";
      } elseif ($price_min) {
        $sql .= "
          HAVING (
            MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END) >= " . $price_min . ")";
      } elseif ($price_max) {
        $sql .= "
          HAVING (
            MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END) <= " . $price_max . ")";
      }
    }

    $sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'rating',
			'p.sort_order',
			'p.date_added',
			'p.quantity<>0 DESC, p.sort_order',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

    $product_data = [];

    file_put_contents('search.txt', Json::encode($data, $asArray = true));
    
    $products = Yii::$app->db
      ->createCommand($sql)
      ->queryAll();

    foreach ($products as $product) {
			$product_data[] = self::getProduct($product['id'], $image_type);
		}

		return $product_data;
    
  }

  public static function getRelatedProductIds(int $product_id)
  {
    $data = [];
    $relateds = ProductRelated::find()
      ->alias('pr')
      ->select(['pr.related_id'])
      ->leftJoin('oc_product p', 'p.id = pr.product_id')
      ->where(['pr.product_id' => (int)$product_id, 'p.status' => 1])
      ->andWhere(['<=', 'p.date_available', time()])
      ->all();

    foreach ($relateds as $related) {
      $data[] = (int)$related->related_id;
    }
    
    return $data;
  }

  public static function getRelatedProducts(int $product_id)
  {
    $data = [];
    foreach (Product::getRelatedProductIds($product_id) as $related_id) {
      $product = Product::getProduct($related_id); 
      Product::fillProduct($product, true);
      $data[] = $product;
    }
    
    return $data;
  }

  public static function getProductSpecials(array $data = [], string $image_type = 'thumbs_catalog') 
  {
		$sql = "
      SELECT DISTINCT 
        ps.product_id, 
        (SELECT AVG(rating) FROM oc_review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating 
      FROM oc_product_special ps 
      LEFT JOIN oc_product p ON (ps.product_id = p.id) 
      LEFT JOIN oc_product_description pd ON (p.id = pd.product_id) 
      WHERE 
        p.status = '1' AND 
        p.date_available <= UNIX_TIMESTAMP(NOW()) AND 
        ps.customer_group_id = '" . (int)Yii::$app->shopConfig->getParam('customer_group_id') . "' AND 
        ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
      GROUP BY 
        ps.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'discount',
			'rating',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else if ($data['sort'] == 'discount') {
				$sql .= " ORDER BY ((p.price - ps.price) / p.price)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

    $product_data = [];

    $products = Yii::$app->db
      ->createCommand($sql)
      ->queryAll();

    foreach ($products as $product) {
			$product_data[] = self::getProduct($product['product_id'], $image_type);
		}

		return $product_data;
	}

  public static function getTotalProducts($data = array()) {

    $attributes = [];
    $options = [];
    $filters = [];
    $brands = [];
    $colors = [];

    $attribute_aliases = Attribute::getAliases();
    $options_aliases = Option::getAliases();
    $filters_aliases = FilterGroup::getAliases();

    foreach ($attribute_aliases as $alias) {
      if (!empty($data[$alias])) $attributes[] = $alias;
    }
    foreach ($options_aliases as $alias) {
      if (!empty($data[$alias])) $options[] = $alias;
    }
    foreach ($filters_aliases as $alias) {
      if (!empty($data[$alias])) $filters[] = $alias;
    }
    if (!empty($data['brand'])) $brands[] = 'brand';
    if (!empty($data['color'])) $colors[] = 'color';

    if (!empty($data['filter_price'])) {
			$sql = "
        SELECT COUNT(*) as total 
        FROM 
          (SELECT 
            ( SELECT price 
              FROM oc_product_discount pd2 
              WHERE 
                pd2.product_id = p.id AND 
                pd2.customer_group_id = '" . (int)Yii::$app->shopConfig->getParam('customer_group_id') . "' AND 
                pd2.quantity = '1' AND 
                ((pd2.date_start = 0 OR pd2.date_start < UNIX_TIMESTAMP(NOW())) AND 
                (pd2.date_end = 0 OR pd2.date_end > UNIX_TIMESTAMP(NOW()))) 
              ORDER BY pd2.priority ASC, pd2.price ASC 
              LIMIT 1
            ) AS discount, 
            ( SELECT price 
              FROM oc_product_special ps 
              WHERE 
                ps.product_id = p.id AND 
                ps.customer_group_id = '" . (int)Yii::$app->shopConfig->getParam('customer_group_id') . "' AND 
                ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND 
                (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
              ORDER BY ps.priority ASC, ps.price ASC 
              LIMIT 1
            ) AS special";

    } elseif (!empty($data['prices_only'])) {

			$sql = "
        SELECT 
          MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price1 END) AS max_price, 
          MIN(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price1 END) AS min_price 
        FROM 
          (SELECT 
            ( SELECT price 
              FROM oc_product_discount pd2 
              WHERE 
                pd2.product_id = p.id AND 
                pd2.customer_group_id = '" . (int)Yii::$app->shopConfig->getParam('customer_group_id') . "' AND 
                pd2.quantity = '1' 
                AND ((pd2.date_start = 0 OR pd2.date_start < UNIX_TIMESTAMP(NOW())) AND 
                (pd2.date_end = 0 OR pd2.date_end > UNIX_TIMESTAMP(NOW()))) 
              ORDER BY pd2.priority ASC, pd2.price ASC 
              LIMIT 1
            ) AS discount, 
            ( SELECT price 
              FROM oc_product_special ps 
              WHERE 
                ps.product_id = p.id AND 
                ps.customer_group_id = '" . (int)Yii::$app->shopConfig->getParam('customer_group_id') . "' AND 
                ((ps.date_start = 0 OR ps.date_start < UNIX_TIMESTAMP(NOW())) AND 
                (ps.date_end = 0 OR ps.date_end > UNIX_TIMESTAMP(NOW()))) 
              ORDER BY ps.priority ASC, ps.price ASC 
              LIMIT 1
            ) AS special, 
            p.price AS price1";

    } else {
        $sql = "
          SELECT COUNT(DISTINCT p.id) AS total";
    }

		if (!empty($data['filter_category_id'])) {
      if (!empty($data['filter_sub_category'])) {
        $sql .= " FROM oc_category_path cp LEFT JOIN oc_product_to_category p2c ON (cp.category_id = p2c.category_id)";
      } else {
        $sql .= " FROM oc_product_to_category p2c ON (cp.category_id = p2c.category_id)";
      }
      $sql .= " LEFT JOIN oc_product p ON (p2c.product_id = p.id)";
		} else {
			$sql .= " FROM oc_product p";
		}

    if (!empty($attributes)) {
      foreach ($attributes as $key => $attribute) {
        $sql .= "
          LEFT JOIN oc_product_attribute pa" . ($key + 1) . " 
            ON (p.id = pa" . ($key + 1) . ".product_id AND pa" . ($key + 1) . ".language_id = '" . \Yii::$app->language . "') 
          LEFT JOIN oc_attribute a" . ($key + 1) . " 
            ON (pa" . ($key + 1) . ".attribute_id = a" . ($key + 1) . ".id)";
      }
    }
    if (!empty($options)) {
      foreach ($options as $key => $option) {
        $sql .= "
          LEFT JOIN oc_product_option_value pov" . ($key + 1) . " 
            ON (p.id = pov" . ($key + 1) . ".product_id) 
          LEFT JOIN oc_option_value ov" . ($key + 1) . " 
            ON (ov" . ($key + 1) . ".id = pov" . ($key + 1) . ".option_value_id) 
          LEFT JOIN oc_option o" . ($key + 1) . " 
            ON(o" . ($key + 1) . ".id = ov" . ($key + 1) . ".option_id)";
      }
    }
    if (!empty($filters)) {
      foreach ($filters as $key => $filter) {
        $sql .= "
          LEFT JOIN oc_product_filter pf" . ($key + 1) . " 
            ON (p.id = pf" . ($key + 1) . ".product_id) 
          LEFT JOIN oc_filter f" . ($key + 1) . " 
            ON (pf" . ($key + 1) . ".filter_id = f" . ($key + 1) . ".id) 
          LEFT JOIN oc_filter_group fg" . ($key + 1) . " 
            ON(f" . ($key + 1) . ".filter_group_id = fg" . ($key + 1) . ".id)";
        }
    }

    if (!empty($colors)) {
        $sql .= " 
        LEFT JOIN oc_pcolor pcl ON (pcl.id = p.pcolor_id)";
    }

    if (!empty($brands)) {
      $sql .= "
        LEFT JOIN oc_manufacturer m ON (p.manufacturer_id = m.id)";
    }

    $sql .= "
      LEFT JOIN oc_product_description pd ON (p.id = pd.product_id) 
      WHERE 
        pd.language_id = '" . \Yii::$app->language . "' AND 
        p.status = '1' AND 
        p.date_available <= UNIX_TIMESTAMP(NOW())"; 

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

    if (!empty($colors)) {
      if (strpos($data['color'], ',')) {
        $arr_color = explode(',', $data['color']);
        $str = Yii::$app->db->quoteValue(array_shift($arr_color));
        foreach ($arr_color as $item) {
          $str .= ", " . Yii::$app->db->quoteValue($item);
        }
				$sql .= " AND pcl.alias IN (" . $str . ")";
			} else {
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

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE " .  Yii::$app->db->quoteValue("%" . $word . "%");
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE " .  Yii::$app->db->quoteValue("%" . $data['filter_name'] . "%");
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE " .  Yii::$app->db->quoteValue("%" . $word . "%");
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {

        $sql .= " OR LCASE(p.model) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.sku) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.upc) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.ean) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.jan) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.isbn) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));
				$sql .= " OR LCASE(p.mpn) = " .Yii::$app->db->quoteValue(mb_strtolower($data['filter_name'], 'UTF-8'));

      }

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		if (!Yii::$app->shopConfig->getParam('show_zero_quantity')) {
			$sql .= " AND p.quantity > 0";
		}

    if (!empty($data['filter_price'])) {
      $prices = explode('-', $data['filter_price']);
      $price_min = (float)$prices[0] 
        ? (float)Yii::$app->currency->convert((float)$prices[0], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency'))
        : 0;
      $price_max = (float)$prices[1] 
        ? (float)Yii::$app->currency->convert((float)$prices[1], Yii::$app->currency->getCurrent(), Yii::$app->shopConfig->getParam('currency'))
        : 0;

      if ($price_min & $price_max) {
				$sql .= "
            GROUP BY p.id 
            HAVING (
              MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END) >= " . $price_min . " AND 
              MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END) <= " . $price_max . ")
          ) as x";
      } elseif ($price_min) {
				$sql .= "
            GROUP BY p.id 
            HAVING (
              MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END) >= " . $price_min . ")
          ) as x";
			} elseif ($price_max) {
				$sql .= "
            GROUP BY p.id 
            HAVING (
              MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END) <= " . $price_max . ")
          ) as x";
			}
    }
    if (!empty($data['prices_only'])) {
        $sql .= " GROUP BY p.id) as x";
    }


		//file_put_contents('11115.txt', $sql, FILE_APPEND);
    $results = Yii::$app->db
      ->createCommand($sql)
      ->queryOne();

    if (!empty($data['prices_only'])) {
      return array(
        'max_price' => $results['max_price'],
        'min_price' => $results['min_price'],
      );
    } else {
      return $results['total'];
    }
	}

  public static function getProductsMinMaxPrices($data = array()) {
    unset($data['filter_price']);
    $data['prices_only'] = 1;
    return self::getTotalProducts($data);
  }


}
