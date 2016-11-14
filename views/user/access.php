<?php
/**
 * @var $this  \yii\web\View
 * @var $lists \app\models\ObjectsList[]
 * @var $user  \app\models\User
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
?>

<div class="row">
	<div class="col-md-12">
		<? foreach ($lists as $list): ?>
			<div class="col-md-12">
				<label>
					<?= Html::checkbox('lists', in_array($list->id, array_keys(ArrayHelper::map($user->lists, 'id', 'name'))), [
						'id' => $list->id,
					]) ?>
					<?= $list->name ?>
				</label>
			</div>
		<? endforeach ?>
        <div class="col-md-12">
            <label>
                <?= Html::checkbox('isEdit', $user->isEdit) ?>
                Редактирование
            </label>
        </div>

		<button type="button" class="btn btn-success" onclick="saveAccess(<?= $user->id ?>)">Сохранить</button>
	</div>
</div>
