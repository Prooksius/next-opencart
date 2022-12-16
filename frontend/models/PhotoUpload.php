<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 30.04.2020
 * Time: 8:27
 */

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class PhotoUpload extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $picture;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['picture'], 'image', 'extensions' => 'png, jpg', 'minWidth' => 200, 'minHeight' => 200],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $new_file = '/upload/image/avatars/' . $this->picture->baseName . '.' . $this->picture->extension;
            $new_path = dirname(Yii::$app->basePath) . '/upload/image/avatars/' . $this->picture->baseName . '.' . $this->picture->extension;
            try {
                $res = $this->picture->saveAs($new_path);
                if ($res) {
                    return $new_file;
                }
                return false;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }
}