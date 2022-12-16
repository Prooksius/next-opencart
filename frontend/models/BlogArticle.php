<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_newsblog_article".
 */
class BlogArticle extends \yii\db\ActiveRecord
{

  public $main_category_id;

  private $_relatedIds;

  public $name;
  public $preview;
  public $description;
  public $meta_title;
  public $tag;
  public $meta_h1;
  public $meta_description;
  public $meta_keyword;
  public $image_count;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_newsblog_article';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['alias', 'image'], 'string'],
      [['date_available', 'sort_order', 'viewed', 'status', 'featured'], 'integer'],
    ];
  }

  public static function getMainCategoryId(int $article_id)
  {
    $main_category = BlogArticleToCategory::find()
      ->select('category_id')
      ->where(['article_id' => (int)$article_id, 'main_category' => 1])
      ->one();

    if ($main_category instanceof BlogArticleToCategory) {
      return (int)$main_category->category_id;
    }

    return 0;
  }

  public function fetchMainCategoryId()
  {
    $main_category = BlogArticleToCategory::find()
      ->select('category_id')
      ->where(['article_id' => $this->id, 'main_category' => 1])
      ->one();

    if ($main_category instanceof BlogArticleToCategory) {
      return (int)$main_category->category_id;
    }

    return 0;
  }

  public static function getBreadcrumbs($article)
  {
    $breadcrumbs = [
      [
        'title' => Translation::getTranslation('Blog'),
        'href' => '/blog',
      ],
    ];

    $category = BlogCategory::getCategory(self::getMainCategoryId($article['id']));
    $href = ['blog'];

    $breadcrumbs_rev = [];
    while ($category) {
      $breadcrumbs_rev[] = [
        'title' => $category['name'],
        'alias' => $category['alias'],
      ];
      $category = BlogCategory::getCategory((int)$category['parent_id']);
    }

    foreach (array_reverse($breadcrumbs_rev) as $item) {
      $href[] = $item['alias'];
      $breadcrumbs[] = [
        'title' => $item['title'],
        'href' => '/'. implode('/', $href),
      ];
    }

    $breadcrumbs[] = [
      'title' => $article['name'],
      'href' => '',
    ];

    return $breadcrumbs;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getRelateds()
  {
    $products = [];

    foreach (ArrayHelper::getColumn(
      BlogArticleRelated::find()
        ->where(['article_id' => $this->id])
        ->all(), 
      'related_id') as $related_id) {
        $product = Product::getProduct($related_id);
        Product::fillProduct($product, true);
        $products[] = $product;
      }

      return $products;
  }

  public static function getArticle($id, $tile = true)
  {
    return self::_getArticle(['id' => (int)$id, 'tile' => $tile]);
  }

  public static function getArticleBySlug($slug, $tile = true)
  {
    return self::_getArticle(['alias' => $slug, 'tile' => $tile]);
  }

  private static function _getArticle(array $param)
  {
    $articleQuery = self::find()
      ->alias('a')
      ->select([
        'a.*',
        'ad.*',
      ])
      ->leftJoin('oc_newsblog_article_description ad', 'ad.article_id = a.id AND ad.language_id = "' . Yii::$app->language . '"')
      ->where(['a.status' => 1])
      ->andWhere('date_available <= UNIX_TIMESTAMP(NOW())');

    if (!empty($param['id'])) {
      $articleQuery->andWhere(['a.id' => (int)$param['id']]);
    } elseif (!empty($param['alias'])) {
      $articleQuery->andWhere(['a.alias' => $param['alias']]);
    } else {
      return false;
    }

    $article = $articleQuery->one();
    
    if ($article instanceof self) {
      return [
        'id' => $article->id,
        'alias' => $article->alias,
        'updated_at' => Yii::$app->formatter->format((int)$article->updated_at, 'date'),
        'name' => $article->name,
        'image' => $article->image,
        'preview' => $article->preview,
        'description' => $article->description,
        'tag' => $article->tag,
        'meta_title' => $article->meta_title,
        'meta_h1' => $article->meta_h1,
        'meta_description' => $article->meta_description,
        'meta_keyword' => $article->meta_keyword,
        'meta_keyword' => $article->meta_keyword,
        'images' => $param['tile'] ? [] : BlogArticleImage::getArticleImages($article->id, 'thumbs_article'),
        'products' => $param['tile'] ? [] : $article->getRelateds(),
      ];
    }

    return false;
  }

  public static function getArticles(array $data)
  {
		$sql = "SELECT a.id";

		if (!empty($data['filter_category_id']) || !empty($data['filter_categories'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM oc_newsblog_category_path cp LEFT JOIN oc_newsblog_article_to_category a2c ON (cp.category_id = a2c.category_id)";
			} else {
				$sql .= " FROM oc_newsblog_article_to_category a2c";
			}

      $sql .= " LEFT JOIN oc_newsblog_article a ON (a2c.article_id = a.id)";

		} else {
			$sql .= " FROM oc_newsblog_article a";
		}

		$sql .= " LEFT JOIN oc_newsblog_article_description ad ON (a.id = ad.article_id)

		WHERE ad.language_id = " . Yii::$app->db->quoteValue(Yii::$app->language) . " AND
		a.status = '1' AND
		a.date_available <= UNIX_TIMESTAMP(NOW())";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND a2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		if (!empty($data['filter_featured'])) {
      $sql .= " AND a.featured = '" . (int)$data['filter_featured'] . "'";
		}

		if (!empty($data['filter_categories'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id in (" . implode(',',$data['filter_categories']) . ")";
			} else {
				$sql .= " AND a2c.category_id in (" . implode(',',$data['filter_categories']) . ")";
			}
		}

		$sql .= " GROUP BY a.id";

		if (isset($data['sort'])) {
			if ($data['sort'] == 'ad.name') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY a.sort_order";
		}

		if (isset($data['order'])) {
			$sql .= " ".$data['order'];
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 10;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$article_data = array();

    $articles = Yii::$app->db
      ->createCommand($sql)
      ->queryAll();

		foreach ($articles as $result) {
			$article_data[] = self::getArticle((int)$result['id']);
		}

		return $article_data;
  }

	public static function getTotalArticles(array $data = []) {
		$sql = "SELECT COUNT(DISTINCT a.id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM oc_newsblog_category_path bcp LEFT JOIN oc_newsblog_article_to_category a2c ON (bcp.category_id = a2c.category_id)";
			} else {
				$sql .= " FROM oc_newsblog_article_to_category a2c";
			}

      $sql .= " LEFT JOIN oc_newsblog_article a ON (a2c.article_id = a.id)";
		} else {
			$sql .= " FROM oc_newsblog_article a";
		}

		$sql .= "
      LEFT JOIN oc_newsblog_article_description ad ON (a.id = ad.article_id) 
        WHERE 
          ad.language_id = " . Yii::$app->db->quoteValue(Yii::$app->language) . " AND 
          a.status = '1' AND 
          a.date_available <= UNIX_TIMESTAMP(NOW())";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND bcp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND a2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "ad.name LIKE " . Yii::$app->db->quoteValue('%'.$word.'%');
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR ad.description LIKE " . Yii::$app->db->quoteValue('%'.$data['filter_name'].'%');
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "ad.tag LIKE " .  Yii::$app->db->quoteValue("%" . $word . "%");
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			$sql .= ")";
		}

    $results = Yii::$app->db
      ->createCommand($sql)
      ->queryOne();

		return $results['total'];
	}
}
