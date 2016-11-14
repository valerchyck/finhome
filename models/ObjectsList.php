<?php
namespace app\models;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "objects_list".
 *
 * @property string $id
 * @property string $name
 * @property string $owner
 * @property integer $phone_first
 * @property integer $phone_second
 * @property string $link1
 * @property string $link2
 * @property string $link3
 * @property array $photos
 * @property string $font_color
 * @property string $folder
 */
class ObjectsList extends \yii\db\ActiveRecord {
	/**
	 * @var UploadedFile[]
	 */
	public $images;

	public function init() {
		parent::init();

		$this->folder = \Yii::$app->security->generateRandomString();
	}

	public static function tableName() {
        return 'objects_list';
    }

    public function rules() {
        return [
            [['name', 'owner'], 'required'],
            [['phone_first', 'phone_second'], 'integer'],
            [['link1', 'link2', 'link3', 'font_color', 'folder'], 'string'],
            [['name', 'owner'], 'string', 'max' => 100],
	        [['images'], 'file', 'extensions' => 'png, jpg', 'maxFiles' => 5],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Объект',
            'owner' => 'Ответственный',
            'phone_first' => 'Номер 1',
            'phone_second' => 'Номер 2',
            'link1' => 'Ссылка 1',
            'link2' => 'Ссылка 2',
            'link3' => 'Ссылка 3',
            'images' => 'Фото (максимум 5)',
            'font_color' => 'Цвет шрифта',
        ];
    }

	public function upload() {
		$path = "upload/objects/{$this->folder}/";
		FileHelper::createDirectory($path);

		foreach ($this->images as $img)
			$img->saveAs($path . $img->baseName . '.' . $img->extension);

		$this->images = null;
	}

	public function getPhotos() {
		$path  = "upload/objects/{$this->folder}/";
		if (!file_exists($path))
			return [];

		$files = FileHelper::findFiles($path);
		foreach ($files as & $file) {
			$file = \Yii::$app->request->hostInfo.'/'.$path.pathinfo($file)['basename'];
		}

		return $files;
	}
}
