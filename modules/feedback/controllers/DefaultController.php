<?php
namespace app\modules\feedback\controllers;

use app\modules\feedback\models\Feedback;
use yii\web\Controller;
use yii\web\UploadedFile;

class DefaultController extends Controller {
    public function actionIndex() {
	    $feedback = new Feedback();

	    if (($data = \Yii::$app->request->post('Feedback')) != null) {
		    $feedback->setAttributes($data);

		    $feedback->files = UploadedFile::getInstances($feedback, 'files');
		    if ($feedback->files != null) {
			    $feedback->upload();
		    }

		    $feedback->save();

		    return $this->redirect('/', 301);
	    }

        return $this->render('index', [
	        'feedback' => $feedback
        ]);
    }
}
