<?php

/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use Yii;
use frontend\models\Notes;
use frontend\components\ApiController;
use frontend\components\Helper;
use yii\data\ActiveDataProvider;

class NotesController extends ApiController
{
    public function actionIndex()
	{
	  return [
		'success' => 1,
	  ];
	}
	
    public function actionGetNotesPage($sort, $page = 1)
    {
		$post = Helper::cleanData(Yii::$app->request->post());
        try {
            $pagesize = isset($post['pagesize']) ? (int)$post['pagesize'] : 20;
            $completed = isset($post['completed']) ? $post['completed'] : null;

            $query = Notes::find()
                ->andFilterWhere(['completed' => $completed]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => $pagesize,
                ],
                'sort' => [
                    'defaultOrder'=>[
                        'date'=>SORT_DESC
                    ],
                ],
            ]);

            $models = $dataProvider->getModels();
            $notes = [];
            foreach ($models as $item) {
				$attrs = $item->attributes;
				$attrs['datetext'] = $item->datetext;
                $notes[] = $attrs;
            }
            $count = $dataProvider->getTotalCount();

            return [
                'success' => 1,
                'notes' => [
                    'list' => $notes,
                    'page' => (int)$page,
                    'count' => $count,
					'completed' => $completed
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

  public function actionGetAllNotes()
  {
	  return [
		'success' => 1,
		'notes' => Notes::getAllNotes(),
	  ];
  }

  public function actionAddNewNote()
  {
	  $post = Helper::cleanData(Yii::$app->request->post());
	  
	  $newNote = new Notes();
	  
	  $newNote->title = $post['title'];
	  $newNote->date = time();
	  $newNote->completed = 0;
	  $newNote->save();
	  $newNote->refresh();
	  
	  $attrs = $newNote->attributes;
	  $attrs['datetext'] = $newNote->datetext;
	  
	  return [
		'success' => 1,
		'note' => $attrs,
	  ];
  }

  public function actionEditNote($id)
  {
    if (Helper::recaptchaCheck()) {
      $post = Helper::cleanData(Yii::$app->request->post());
      
      $editNote = Notes::findOne($id);
      
      $editNote->title = $post['title'];
      $editNote->save();

      $attrs = $editNote->attributes;
      $attrs['datetext'] = $editNote->datetext;
      
      return [
      'success' => 1,
      'note' => $attrs,
      'post' => $post,
      ];
      
    } else {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => 'captcha wrong',
      ];
    }
  }

  public function actionDeleteThisNote($id)
  {
	  $note = Notes::findOne($id);
	  if ($note instanceof Notes) {
		  $note->delete();
		  
		  return [
			'success' => 1,
			'id' => $id,
		  ];		  
	  } else {
		  Yii::$app->response->setStatusCode(404);
		  return [
			'success' => 0,
			'error' => 'Заметка не найдена'
		  ];		  
		  
	  }
  }
}
