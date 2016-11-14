<?php

namespace app\modules\feedback\models;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "feedback".
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $date
 * @property string $text
 * @property integer $status
 * @property string $folder
 */
class Feedback extends \yii\db\ActiveRecord {
	/**
	 * @var UploadedFile[]
	 */
	public $files;

	public function init() {
		parent::init();

		$this->folder = \Yii::$app->security->generateRandomString();
	}

	public static function tableName() {
        return 'feedback';
    }

    public function rules() {
        return [
            [['name', 'email', 'text'], 'required'],
            [['date'], 'safe'],
            [['text'], 'string'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 150],
            [['email'], 'string', 'max' => 255],
	        [['files'], 'file', 'maxFiles' => 5],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'Ид',
            'name' => 'Имя',
            'email' => 'E-Mail',
            'date' => 'Дата',
            'text' => 'Описание',
            'status' => 'Статус',
	        'files' => 'Файлы (максимум 5)',
        ];
    }

	public function beforeSave($insert) {
		$this->date = date('Y-m-d');

		return parent::beforeSave($insert);
	}

	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);

		mail('valerchyck937@gmail.com', 'Feedback from Finhome', "ИД {$this->id}");
	}

	public function upload() {
		$path = "upload/feedback/{$this->folder}/";
		FileHelper::createDirectory($path);

		foreach ($this->files as $img)
			$img->saveAs($path . $img->baseName . '.' . $img->extension);

		$this->files = null;
	}
}
