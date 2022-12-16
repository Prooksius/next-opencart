<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

namespace common\modules\blog\models;

use common\modules\blog\Module;
use common\modules\blog\traits\IActiveStatus;
use common\modules\blog\traits\ModuleTrait;
use common\modules\blog\traits\StatusTrait;
use yii\behaviors\TimestampBehavior;
use common\components\AdjacencyListBehavior;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "blog_comment".
 *
 * @property integer $id
 * @property integer $post_id
 * @property string $content
 * @property string $author
 * @property string $email
 * @property string $url
 * @property string $captcha
 * @property integer $status
 * @property integer $parentId
 * @property integer $level
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property BlogPost $post
 */
class BlogComment extends \yii\db\ActiveRecord
{
    use StatusTrait, ModuleTrait;

    const SCENARIO_ADMIN = 'admin';
    const SCENARIO_USER = 'user';

    public $captcha;

    private $_status;

    /**
     * @var null|array|ActiveRecord[] comment children
     */
    protected $children;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%blog_comment}}';
    }

    /**
     * created_at, updated_at to now()
     * crate_user_id, update_user_id to current login user id
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
            ],
            'adjacencyList' => [
                'class' => AdjacencyListBehavior::className(),
                'parentAttribute' => 'parentId',
                'sortable' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADMIN] = ['post_id', 'content', 'author', 'email', 'status', 'url'];
        $scenarios[self::SCENARIO_USER] = ['content', 'author', 'email', 'parentId', 'level', 'status'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'content', 'author', 'email'], 'required'],
            ['email', 'email'],
            [['author', 'content'], 'filter', 'filter' => 'strip_tags'],
            [['author', 'captcha', 'email'], 'trim'],
            [['post_id', 'status', 'level'], 'integer'],
            ['status', 'default', 'value' => IActiveStatus::STATUS_INACTIVE],
            [['content'], 'string'],
            [['author', 'email', 'url'], 'string', 'max' => 128],
            ['parentId', 'validateParentID'],
            ['level', 'default', 'value' => 1],
        ];
    }

    /**
     * @param $attribute
     */
    public function validateParentID($attribute)
    {
        if (null !== $this->{$attribute}) {
            $parentCommentExist = static::find()
                ->andWhere([
                    'id' => $this->{$attribute},
                    'post_id' => $this->post_id,
                ])
                ->exists();
            if (!$parentCommentExist) {
                $this->addError('content', Module::t('blog', 'Oops, something went wrong. Please try again later.'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('blog', 'ID'),
            'post_id' => Module::t('blog', 'Post ID'),
            'content' => Module::t('blog', 'Content'),
            'author' => Module::t('blog', 'Author'),
            'email' => Module::t('blog', 'Email'),
            'url' => Module::t('blog', 'Url'),
            'status' => Module::t('blog', 'Status'),
            'created_at' => Module::t('blog', 'Created At'),
            'updated_at' => Module::t('blog', 'Updated At'),
            'parentId' => Module::t('blog', 'Parent ID'),
            'level' => Module::t('blog', 'Level'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->parentId > 0 && $this->isNewRecord) {
                $parentNodeLevel = static::find()->select('level')->where(['id' => $this->parentId])->scalar();
                $this->level += (int)$parentNodeLevel;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function saveComment()
    {
        if ($this->validate()) {
            if (empty($this->parentId)) {
                return $this->makeRoot()->save();
            } else {
                $parentComment = static::findOne(['id' => $this->parentId]);
                return $this->appendTo($parentComment)->save();
            }
        }
        return false;
    }

    /**
     * Get comments tree.
     *
     * @param string $entity
     * @param string $entityId
     * @param null $maxLevel
     *
     * @return array|ActiveRecord[]
     */
    public static function getTree($post_id, $maxLevel = null)
    {
        $query = static::find()
            ->alias('c')
            ->andWhere([
                'c.post_id' => $post_id,
            ])
            ->orderBy(['c.parentId' => SORT_ASC, 'c.created_at' => SORT_ASC]);
        if ($maxLevel > 0) {
            $query->andWhere(['<=', 'c.level', $maxLevel]);
        }
        $models = $query->all();
        if (!empty($models)) {
            $models = static::buildTree($models);
        }
        return $models;
    }
    /**
     * Build comments tree.
     *
     * @param array $data comments list
     * @param int $rootID
     *
     * @return array|ActiveRecord[]
     */
    protected static function buildTree(&$data, $rootID = 0)
    {
        $tree = [];
        foreach ($data as $id => $node) {
            if ($node->parentId == $rootID) {
                unset($data[$id]);
                $node->children = self::buildTree($data, $node->id);
                $tree[] = $node;
            }
        }
        return $tree;
    }
    /**
     * @return array|null|ActiveRecord[]
     */
    public function getChildren()
    {
        return $this->children;
    }
    /**
     * @param $value
     */
    public function setChildren($value)
    {
        $this->children = $value;
    }
    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(BlogPost::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlogPost()
    {
        return $this->hasOne(BlogPost::className(), ['id' => 'post_id']);
    }

    /**
     * @return string
     */
    public function getAuthorLink()
    {
        if (!empty($this->url)) {
            return Html::a(Html::encode($this->author), $this->url);
        } else {
            return Html::encode($this->author);
        }
    }

    /**
     * @param null $post
     * @return string
     */
    public function getUrl($post = null)
    {
        if ($post === null) {
            $post = $this->post;
        }
        return $post->url . '#c' . $this->id;
    }

    /**
     * @return int
     */
    public function getCommentsCount()
    {
        return (int) static::find()
            ->andWhere(['post_id' => $this->post_id, 'status' => IActiveStatus::STATUS_ACTIVE])
            ->count();
    }

    /**
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findRecentComments($limit = 10)
    {
        return self::find()->joinWith('blogPost')->where([
            '{{%blog_comment}}.status' => IActiveStatus::STATUS_ACTIVE,
            '{{%blog_post}}.status' => IActiveStatus::STATUS_ACTIVE,
        ])->orderBy([
            'created_at' => SORT_DESC
        ])->limit($limit)->all();
    }

    /**
     * @return string
     */
    public function getMaskedEmail()
    {
        list($email_username, $email_domain) = explode('@', $this->email);
        $masked_email_username = preg_replace('/(.)./', "$1*", $email_username);
        return implode('@', array($masked_email_username, $email_domain));
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return ($this->status == IActiveStatus::STATUS_ACTIVE) ? $this->content : StringHelper::truncate($this->content, 15);
    }
}
