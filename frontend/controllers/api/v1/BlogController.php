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
use frontend\models\BlogArticle;
use frontend\models\BlogCategory;
use frontend\models\Translation;
use yii\data\ActiveDataProvider;
use himiklab\thumbnail\EasyThumbnailImage;

class BlogController extends PublicApiController
{
  private $sorts = [
    'default' => 'a.created_at DESC',
    'popularity' => 'a.created_at ASC',
  ];

  public function actionIndex()
  {

    $get = Helper::cleanData(Yii::$app->request->get());

    $page = !empty($get['page']) ? (int)$get['page'] : 1;
    $limit = !empty($get['limit']) ? (int)$get['limit'] : 6;

    $slug = !empty($get['slug']) ? $get['slug'] : '';
    $slugs = '';
    $slugs_arr = [];
    if (strpos($slug, ',') !== false) {
      $slugs = explode(',', $slug);
      $slug = array_pop($slugs);
      $slugs_arr = $slugs;
      $slugs = implode('/', $slugs);
    }

    if (!empty($get['sort'])) {
      $sorts = $this->sorts[$get['sort']];
    } else {
      $sorts = $this->sorts['default'];
    }
    $sort_arr = explode(' ', $sorts);
    $sort = $sort_arr[0];
    $order = $sort_arr[1];

    $current_category = null;
    if ($slug) {
      $current_category = BlogCategory::getCategoryBySlug($slug);
      $current_category['thumb'] =  $current_category['image'] ? EasyThumbnailImage::thumbnailFileUrl(
        '@root' . $current_category['image'],
        400,
        400,
        EasyThumbnailImage::THUMBNAIL_INSET
      ) : '/upload/img/banners/no_image.png';
    } 

    if ($current_category) {
      $childs = BlogCategory::getCategories((int)$current_category['id']);

      $filter_data = array(
        'filter_category_id'  => $current_category['id'],
        'sort'                => $sort,
        'order'               => $order,
        'start'               => ($page - 1) * $limit,
        'limit'               => $limit,
      );

      $articles = BlogArticle::getArticles($filter_data);
      $articlesCount = BlogArticle::getTotalArticles($filter_data);

    } else {
      $childs = BlogCategory::getCategories(0);

      $filter_data = array(
        'filter_featured'     => true,
        'sort'                => 'a.created_at',
        'order'               => 'DESC',
        'start'               => 0,
        'limit'               => $limit,
      );

      $articles = BlogArticle::getArticles($filter_data);
      $articlesCount = count($articles);
    }

    foreach ($childs as &$child) {
      $child['thumb'] =  $child['image'] ? EasyThumbnailImage::thumbnailFileUrl(
        '@root' . $child['image'],
        600,
        600,
        EasyThumbnailImage::THUMBNAIL_OUTBOUND
      ) : '/upload/img/banners/no_image.png';
      $child['parent_path'] = ($slugs ? $slugs . '/' : '') . ($slug ? $slug . '/' : '') .$child['alias']; 
    }

    foreach ($articles as &$child) {
      unset($child['description']);

      $child['thumb'] =  $child['image'] ? EasyThumbnailImage::thumbnailFileUrl(
        '@root' . $child['image'],
        600,
        600,
        EasyThumbnailImage::THUMBNAIL_OUTBOUND
      ) : '/upload/img/banners/no_image.png';
    }

    return [
      'success' => 1,
      'breadcrumbs' => BlogCategory::getBreadcrumbs($slugs_arr, $slug),
      'blog' => [
        'categories' => [
          'current' => $current_category,
          'childs' => $childs,
        ],
      ],
      'articles' => [
        'list' => $articles,
        'page' => (int)$page,
        'count' => $articlesCount,
        'limit' => $limit,
      ],
    ];
  }

  public function actionPost()
  {
    $get = Helper::cleanData(Yii::$app->request->get());

    $slug = !empty($get['slug']) ? $get['slug'] : '';

    $post = BlogArticle::getArticleBySlug($slug, false);

    if ($post) {
      return [
        'success' => 1,
        'breadcrumbs' => BlogArticle::getBreadcrumbs($post),
        'post' => $post,
      ];
    }

    Yii::$app->response->setStatusCode(404);
    return [
      'success' => 0,
      'error' => 'Article not found',
    ];
  }

  public function actionArticles()
  {
    $get = Helper::cleanData(Yii::$app->request->get());

    $page = !empty($get['page']) ? (int)$get['page'] : 1;
    $limit = !empty($get['limit']) ? (int)$get['limit'] : 6;

    $slug = !empty($get['slug']) ? $get['slug'] : '';
    $slugs = '';
    if (strpos($slug, ',') !== false) {
      $slugs = explode(',', $slug);
      $slug = array_pop($slugs);
      $slugs_arr = $slugs;
      $slugs = implode('/', $slugs);
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
        $current_category = BlogCategory::getCategoryBySlug($slug);
        $category_id = (int)$current_category['id'];
      }

      $filter_data = array(
        'filter_category_id'  => $category_id,
        'sort'                => $sort,
        'order'               => $order,
        'start'               => ($page - 1) * $limit,
        'limit'               => $limit,
      );

      $articles = BlogArticle::getArticles($filter_data);
      $count = BlogArticle::getTotalArticles($filter_data);

      foreach ($articles as &$child) {
        unset($child['description']);

        $child['thumb'] =  $child['image'] ? EasyThumbnailImage::thumbnailFileUrl(
          '@root' . $child['image'],
          600,
          600,
          EasyThumbnailImage::THUMBNAIL_OUTBOUND
        ) : '/upload/img/banners/no_image.png';
      }

      return [
        'success' => 1,
        'articles' => [
          'list' => $articles,
          'page' => (int)$page,
          'count' => $count,
          'limit' => $limit,
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
