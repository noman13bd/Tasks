<?php
namespace Task;

use Db;
use Task\Route;
use Task\Database\DbQuery;
use Task\Task\Task as TaskObject;
use Task\Util\ArrayUtils;
use Task\Validate;

class Task extends Route {

	public function addTask() {
		$api = $this->api;
        $payload = $api->request()->post();

		$validationErrMssg = $this->validateParams($payload);

		if(!empty($validationErrMssg)) {
			return $api->response([
				'message' => $validationErrMssg
			])->setStatus(400);
		}

		$title = ArrayUtils::get($payload, 'title');
		$points = ArrayUtils::get($payload, 'points');
		$parent_id = ArrayUtils::get($payload, 'parent_id');
		$user_id = ArrayUtils::get($payload, 'user_id');
		$email = ArrayUtils::get($payload, 'email');
		$is_done = ArrayUtils::get($payload, 'is_done');		

		$task = new TaskObject();
		$task->title = $title;
		$task->parent_id = $parent_id;
        $task->user_id = $user_id;
        $task->points = $points;
        $task->is_done = $is_done;
        

		$ok = $task->save();

		if (!$ok) {
			return $api->response([
				'message' => 'Unable to create task'
			])->setStatus(400);
		}

		return $api->response([
			'id' => $task->id,
			'parent_id' => $task->parent_id,
			'user_id' => $task->user_id,
			'title' => $task->title,
			'points' => $task->points,
			'is_done' => $task->is_done,
			'created_at' => $task->created_at,
			'updated_at' => $task->updated_at,
		])->setStatus(201);
	}

	public function updateTask($taskId ) {
		$api = $this->api;
		$payload = $api->request()->post();
		
		$task = new TaskObject( (int) $taskId );
		if(!Validate::isLoadedObject($task)) {
			return $api->response([
				'message' => 'Task Not Found'
			])->setStatus(400);
		}

		$validationErrMssg = $this->validateParams($payload);

		if(!empty($validationErrMssg)) {
			return $api->response([
				'message' => $validationErrMssg
			])->setStatus(400);
		}

		$title = ArrayUtils::get($payload, 'title');
		$points = ArrayUtils::get($payload, 'points');
		$parent_id = ArrayUtils::get($payload, 'parent_id');
		$user_id = ArrayUtils::get($payload, 'user_id');
		$email = ArrayUtils::get($payload, 'email');
		$is_done = ArrayUtils::get($payload, 'is_done');		

		$task->title = $title;
		$task->parent_id = $parent_id;
        $task->user_id = $user_id;
        $task->points = $points;
        $task->is_done = $is_done;
        

		$ok = $task->save();

		if (!$ok) {
			return $api->response([
				'message' => 'Unable to create task'
			])->setStatus(400);
		}

		return $api->response([
			'id' => $task->id,
			'parent_id' => $task->parent_id,
			'user_id' => $task->user_id,
			'title' => $task->title,
			'points' => $task->points,
			'is_done' => $task->is_done,
			'created_at' => $task->created_at,
			'updated_at' => $task->updated_at,
		])->setStatus(201);
	}

	private function validateParams($payload) {
		$api = $this->api;
		
		$title = ArrayUtils::get($payload, 'title');
		$points = ArrayUtils::get($payload, 'points');
		$parent_id = ArrayUtils::get($payload, 'parent_id');
		$user_id = ArrayUtils::get($payload, 'user_id');
		$email = ArrayUtils::get($payload, 'email');
		$is_done = ArrayUtils::get($payload, 'is_done');

		if(!empty($parent_id)) {
			$parenttask = new TaskObject( (int) $parent_id );
			if(!Validate::isLoadedObject($parenttask)) {
				return 'Invalid parent_id';
			} else if($parenttask->user_id != $user_id) {
				return 'Invalid parent_id (Parent task does not belong to same user)';
			} else {
				// check if max_depth less than 5 
				$depth = 1;
				$parenttask1 = new TaskObject( (int) $parenttask->parent_id );
				if(!empty($parenttask1->parent_id)) {
					$depth++;
					$parenttask2 = new TaskObject( (int) $parenttask1->parent_id );
					if(!empty($parenttask2->parent_id)) {
						$depth++;
						$parenttask3 = new TaskObject( (int) $parenttask2->parent_id );
						if(!empty($parenttask3->parent_id)) {
							$depth++;
							$parenttask4 = new TaskObject( (int) $parenttask3->parent_id );
							if(!empty($parenttask4->parent_id)) {
								$depth++;
								$parenttask5 = new TaskObject( (int) $parenttask4->parent_id );
							}
						}
					}
				} 
				if($depth >= 5) {
					return 'parent_id: '.$parent_id.' already reached to maximum 5 sub tasks';
				}
			}
		}

		if(!Validate::isInt($user_id)) {
			return 'Enter a valid user_id';
        } else {
			if(!Validate::isValidUser($user_id, $email)) {
				return 'Enter a valid user_id';
			}
		}
        
        // check if user is existing one

        if (empty($title)) {
			return 'Task title is required';
        }
        
        if(!Validate::isInt($points)) {
			return 'Points must be an integer';
        } else {
            if($points < 1 || $points > 10) {
                return 'Points must be in between 1 and 10';
            }
        }
        
        if(!Validate::isInt($is_done)) {
			return 'is_done must be an integer';
        } else {
            if($is_done < 0 || $is_done > 1) {
                return 'is_done must be either 0 or 1';
            }
        }
	}
}


