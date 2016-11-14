<?php
namespace app\components;

use yii\rbac\Assignment;

class DbManager extends \yii\rbac\DbManager {
	public function assign($role, $userId) {
		$assignment = new Assignment([
			'userId'    => $userId,
			'roleName'  => $role->name,
			'createdAt' => time(),
		]);

		$this->db->createCommand(
			"REPLACE INTO `auth_assignment` VALUES('{$assignment->roleName}', {$assignment->userId}, '{$assignment->createdAt}')"
		)->execute();

		return $assignment;
	}
}
