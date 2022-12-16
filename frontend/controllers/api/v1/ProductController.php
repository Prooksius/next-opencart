<?php

/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use Yii;
use frontend\components\Helper;
use frontend\components\PublicApiController;
use frontend\models\Attribute;
use frontend\models\Category;
use frontend\models\FilterGroup;
use frontend\models\Option;
use frontend\models\Product;
use frontend\models\ProductAttribute;
use frontend\models\ProductColorRelated;
use frontend\models\ProductFilter;
use frontend\models\ProductImage;
use frontend\models\ProductOption;
use frontend\models\ProductRelated;
use yii\data\ActiveDataProvider;
use himiklab\thumbnail\EasyThumbnailImage;
use yii\helpers\Json;
use common\components\SphinxClient;
use common\models\SphinxProduct;

class ProductController extends PublicApiController
{

  private $sorts = [
    'default' => 'p.sort_order ASC',
    'price' => 'p.price ASC',
    '-price' => 'p.price DESC',
  ];

  public function actionIndex()
  {

    return [
      'success' => 1,
    ];
  }

  public function actionProduct($slug)
	{

    $product = Product::getProductBySlug($slug, 'thumbs_product_big');
    if ($product) {

      Product::fillProduct($product);
      $product['related'] = Product::getRelatedProducts((int)$product['id']);
      $product['other_colors'] = ProductColorRelated::getProductColors((int)$product['id']);

      return [
        'success' => 1,
        'breadcrumbs' => Product::getBreadcrumbs($product),
        'product' => $product,
      ];
    }

    Yii::$app->response->setStatusCode(404);
    return [
      'success' => 0,
      'error' => 'Product not found',
    ];

  }
	
  public function actionPage()
  {

    $post = Helper::cleanData(Yii::$app->request->post());
    $get = Helper::cleanData(Yii::$app->request->get());

    $page = !empty($get['page']) ? (int)$get['page'] : 1;
    $limit = !empty($get['limit']) ? (int)$get['limit'] : 12;

    $search = !empty($get['search']) ? $get['search'] : '';
    $tag = !empty($get['tag']) ? $get['tag'] : '';

    $slug = !empty($get['slug']) ? $get['slug'] : '';
    $slugs = '';
    if (strpos($slug, ',') !== false) {
      $slugs = explode(',', $slug);
      $slug = array_pop($slugs);
      $slugs = implode('/', $slugs);
    }

		$get_attributes = [];
		$get_options = [];
		$get_filters = [];
		$get_brands = [];
		$get_colors = [];
		$get_price = [];

    if ((int)Yii::$app->shopConfig->getParam('filter_show_attributes')) {
			$results = Attribute::getAllAttributes();
			foreach ($results as $result) {
				if (!empty($get[$result['alias']])) {
					$get_attributes[] = $result['alias'];
				}
			}
		}

		if ((int)Yii::$app->shopConfig->getParam('filter_show_options')) {
			$results = Option::getAllOptions();
			foreach ($results as $result) {
				if (!empty($get[$result['alias']])) {
					$get_options[] = $result['alias'];
				}
			}
		}

		if ((int)Yii::$app->shopConfig->getParam('filter_show_filters')) {
			$results = FilterGroup::getAllFilters();
			foreach ($results as $result) {
				if (!empty($get[$result['alias']])) {
					$get_filters[] = $result['alias'];
				}
			}
		}

		if ((int)Yii::$app->shopConfig->getParam('filter_show_brands')) {
			if (!empty($get['brand'])) {
				$get_brands[] = 'brand';
			}
		}

		if ((int)Yii::$app->shopConfig->getParam('filter_show_colors')) {
			if (!empty($get['color'])) {
				$get_colors[] = 'color';
			}
		}

    if (!empty($get['price'])) {
      $get_price[] = 'price';
    }

    if (!empty($get['sort'])) {
      $sorts = $this->sorts[$get['sort']];
    } else {
      $sorts = $this->sorts['default'];
    }
    $sort_arr = explode(' ', $sorts);
    $sort = $sort_arr[0];
    $order = $sort_arr[1];


    try {

      $category_id = 0;
      if ($slug) {
        $current_category = Category::getCategoryBySlug($slug);
        $category_id = $current_category['id'];
      }

      $filter_data = array(
        'filter_category_id'  => $category_id,
        'filter_sub_category' => true,
        'filter_name'         => $search,
        'filter_tag'          => $tag,
        'sort'                => $sort,
        'order'               => $order,
        'start'               => ($page - 1) * $limit,
        'limit'               => $limit,
      );

      foreach ($get_attributes as $get_attribute) {
        $filter_data[$get_attribute] = $get[$get_attribute];
      }
      foreach ($get_options as $get_option) {
        $filter_data[$get_option] = $get[$get_option];
      }
      foreach ($get_filters as $get_filter) {
        $filter_data[$get_filter] = $get[$get_filter];
      }
      foreach ($get_brands as $get_brand) {
        $filter_data[$get_brand] = $get[$get_brand];
      }
      foreach ($get_colors as $get_color) {
        $filter_data[$get_color] = $get[$get_color];
      }
      foreach ($get_price as $prices) {
        $filter_data['filter_'.$prices] = $get[$prices];
      }

      $list = Product::getProducts($filter_data, 'thumbs_catalog');
      $count = Product::getTotalProducts($filter_data);

      $products = [];
      foreach ($list as &$product) {

        Product::fillProduct($product, true);

        $products[] = $product;
      }

      return [
        'success' => 1,
        'products' => [
          'list' => $products,
          'page' => (int)$page,
          'count' => $count,
          'lang' => Yii::$app->language,
        ],
        'get' => $get,
        'post' => $post,
      ];

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }

  public function actionLookup()
  {

    $post = Helper::cleanData(Yii::$app->request->post());
    $get = Helper::cleanData(Yii::$app->request->get());

    $page = !empty($get['page']) ? (int)$get['page'] : 1;
    $limit = !empty($get['limit']) ? (int)$get['limit'] : 12;

    $search = !empty($get['search']) ? $get['search'] : '';

    $slug = !empty($get['slug']) ? $get['slug'] : '';
    $slugs = '';
    if (strpos($slug, ',') !== false) {
      $slugs = explode(',', $slug);
      $slug = array_pop($slugs);
      $slugs = implode('/', $slugs);
    }

    try {

      $searchResults = SphinxProduct::getDb()
        ->createCommand('
          SELECT id 
          FROM next_products 
          WHERE MATCH(\'' . $search . '\') 
          LIMIT 12 
          OPTION 
            ranker=proximity_bm25, 
            field_weights=(product_name=10, product_description=3)'
        )
        ->queryAll();
 
      $count = 0; 
      $products = [];
      foreach ($searchResults as $result) {

        $product = Product::getProduct($result['id']);
        Product::fillProduct($product, true);

        $products[] = $product;
        $count++;
      }

      return [
        'success' => 1,
        'products' => [
          'list' => $products,
          'page' => (int)$page,
          'count' => $count,
          'lang' => Yii::$app->language,
        ],
        'get' => $get,
        'post' => $post,
      ];

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }

  /*
  public function actionAddNewProduct()
  {
	  $post = Helper::cleanData(Yii::$app->request->post());
	  
	  $newProduct = new Product();
	  
	  $newProduct->name = $post['name'];
	  $newProduct->model = $post['model'];
	  $newProduct->sku = $post['sku'];
	  $newProduct->save();
	  $newProduct->refresh();
	  
	  $attrs = $newProduct->attributes;
	  
	  return [
      'success' => 1,
      'product' => $attrs,
	  ];
  }

  public function actionEditProduct($id)
  {
    $post = Helper::cleanData(Yii::$app->request->post());
    if (Helper::recaptchaCheck()) {
      
      $editProduct = Product::findOne($id);
      
      $editProduct->name = $post['name'];
      $editProduct->description = $post['description'];
      $editProduct->model = $post['model'];
      $editProduct->sku = $post['sku'];
      $editProduct->save();

      $attrs = $editProduct->attributes;
      
      return [
        'success' => 1,
        'product' => $attrs,
        'post' => $post,
      ];
      
    } else {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => 'captcha wrong',
        'post' => $post,
      ];
    }
  }

  public function actionDeleteThisProduct($id)
  {
	  $product = Product::findOne($id);
	  if ($product instanceof Product) {
		  $product->delete();
		  
		  return [
        'success' => 1,
        'id' => $id,
		  ];		  
	  } else {
		  Yii::$app->response->setStatusCode(404);
		  return [
        'success' => 0,
        'error' => 'Товар не найден'
		  ];		  
		  
	  }
  }
  */
}
