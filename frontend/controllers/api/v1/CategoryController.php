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
use frontend\models\Category;
use frontend\models\Product;
use yii\data\ActiveDataProvider;
use himiklab\thumbnail\EasyThumbnailImage;

class CategoryController extends PublicApiController
{
  public function actionSomething($page = 1, $pagesize = 2)
  {
    $filter_data = [
      'filter_category_id'  => 20,
      'sort'                => 'p.price',
      'order'               => 'DESC',
      'start'               => $page - 1,
      'limit'               => $pagesize,
    ];


    $list = Product::getProducts($filter_data);
    $count = Product::getTotalProducts($filter_data);

    return [
      'success' => 1,
      'products' => [
        'list' => $list,
        'page' => (int)$page,
        'count' => $count,
      ],
    ];
  }

  public function actionIndex()
  {

    $get = Helper::cleanData(Yii::$app->request->get());

    $slug = !empty($get['slug']) ? $get['slug'] : '';
    $slugs = '';
    $slugs_arr = [];
    if (strpos($slug, ',') !== false) {
      $slugs = explode(',', $slug);
      $slug = array_pop($slugs);
      $slugs_arr = $slugs;
      $slugs = implode('/', $slugs);
    }

    try {
      $current_category = false;
      if ($slug) {
        $current_category = Category::getCategoryBySlug($slug);

        if (!$current_category) {
          throw new \Exception(Yii::t('app', 'Category not found'));
        }

        $current_category['thumb'] =  $current_category['image'] ? EasyThumbnailImage::thumbnailFileUrl(
          '@root' . $current_category['image'],
          400,
          400,
          EasyThumbnailImage::THUMBNAIL_INSET
        ) : '/upload/img/banners/no_image.png';
      } 

      if ($current_category) {
        $roots = Category::getCategories(0);
        $childs = Category::getCategories((int)$current_category['id'], 0, $get);
      } else {
        $childs = Category::getCategories(0, 0, $get);
        $roots = $childs;
      }

      foreach ($childs as &$child) {
        $child['thumb'] =  $child['image'] ? EasyThumbnailImage::thumbnailFileUrl(
          '@root' . $child['image'],
          400,
          400,
          EasyThumbnailImage::THUMBNAIL_INSET
        ) : '/upload/img/banners/no_image.png';
        $child['parent_path'] = ($slugs ? $slugs . '/' : '') . ($slug ? $slug . '/' : '') .$child['alias']; 
      }

      $filter_data = null;
      if ($current_category) {
        unset($get['slug']);
        $filter_data = Category::getFilterData((int)$current_category['id'], $get);
      }

      return [
        'success' => 1,
        'breadcrumbs' => Category::getBreadcrumbs($slugs_arr, $slug),
        'catalog' => [
          'categories' => [
            'current' => $current_category,
            'childs' => $childs,
            'roots' => $roots,
          ],
          'filters' => $filter_data,
          'get' => $get,
          // 'lang' => Yii::$app->language
        ]
      ];    
    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(404);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }

  public function actionRoots()
  {
    $roots = Category::getCategories(0);

    foreach ($roots as &$child) {
      $child['thumb'] =  $child['image'] ? EasyThumbnailImage::thumbnailFileUrl(
        '@root' . $child['image'],
        400,
        400,
        EasyThumbnailImage::THUMBNAIL_INSET
      ) : '/upload/img/banners/no_image.png';
    }

    return [
      'success' => 1,
      'breadcrumbs' => Category::getBreadcrumbs([], ''),
      'roots' => $roots,
    ];

  }

  public function actionProduct($slug)
	{

    $product = Product::getProductBySlug($slug);
    if ($product) {

      $product['price'] = number_format((float)$product['price'], 2, '.', ' ');
      $product['special'] = number_format((float)$product['special'], 2, '.', ' ');

      return [
        'success' => 1,
        'product' => $product,
      ];
    }

    Yii::$app->response->setStatusCode(404);
    return [
      'success' => 0,
      'error' => 'Product not found',
    ];

  }
	
  public function actionPage($page = 1, $pagesize = 10)
  {
    try {

      $filter_data = array(
        'filter_category_id'  => 0,
//        'brand'               => 'apple',
        'sort'                => 'p.price',
        'order'               => 'DESC',
        'start'               => ($page - 1) * $pagesize,
        'limit'               => $pagesize,
      );


      $list = Product::getProducts($filter_data);
      $count = Product::getTotalProducts($filter_data);

      $products = [];
      foreach ($list as $item) {

        $thumb = $item['image'] ? EasyThumbnailImage::thumbnailFileUrl(
          '@root' . $item['image'],
          400,
          400,
          EasyThumbnailImage::THUMBNAIL_INSET
        ) : '/upload/img/banners/no_image.png';

        $attrs = $item;
        unset($attrs['description']);
		    $attrs['image'] = $thumb;
        $products[] = $attrs;
      }

      return [
        'success' => 1,
        'products' => [
          'list' => $products,
          'page' => (int)$page,
          'count' => $count,
          'lang' => Yii::$app->language,
        ],
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
