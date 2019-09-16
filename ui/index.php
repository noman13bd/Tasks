<?php

require "config.php";

$connection = new PDO($dsn, $username, $password, $options);

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => "https://gitlab.iterato.lt/snippets/3/raw",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
		"cache-control: no-cache"
	),
));
$response = curl_exec($curl); 
$response = json_decode($response, true);        
$err = curl_error($curl);
curl_close($curl);

$yesno = array(0 => 'X', 1 => 'V');

if(!empty($response['data'])) {
	echo "<table><td>";
	$response = $response['data'];
	foreach($response as $user) {
		//echo $user['first_name'] . ' ' . $user['last_name'].'<br/>';
		$sql = "SELECT SUM(points) as points, count(CASE WHEN is_done = 1 THEN id ELSE null END) as done_task FROM tasks WHERE user_id = :user_id";
		$statement = $connection->prepare($sql);
		$statement->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		if(!empty($result['points']) && !empty($result['done_task'])) {
			echo "<td style='border: 1px solid; vertical-align: top; text-align: left'>";
			echo "<ul style='padding:10px'>{$user['first_name']} {$user['last_name']} ({$result['done_task']} / {$result['points']})";
			$sql1 = "SELECT id, title, points, is_done FROM tasks WHERE user_id = :user_id AND parent_id = 0";
			$statement1 = $connection->prepare($sql1);
			$statement1->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
			$statement1->execute();
			$result1 = $statement1->fetchAll();

			foreach($result1 as $task) {
				echo "<li style='list-style-position: inside;'> ({$yesno[$task['is_done']]}) {$task['title']} ({$task['points']})</li>";

				$sql2 = "SELECT id, title, points, is_done FROM (SELECT * FROM tasks WHERE user_id = :user_id 
					ORDER BY parent_id, id) tasks, (SELECT @pv := :parent_id) initialisation 
					WHERE find_in_set(parent_id, @pv) > 0 AND @pv := concat(@pv, ',', id)";
				$statement2 = $connection->prepare($sql2);
				$statement2->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
				$statement2->bindParam(':parent_id', $task['id'], PDO::PARAM_INT);
				$statement2->execute();
				$result2 = $statement2->fetchAll();
				echo "<ul>";
				foreach($result2 as $stask1) {
					echo "<li> ({$yesno[$stask1['is_done']]}) {$stask1['title']} ({$stask1['points']})</li>";
				}
				echo "</ul>";
			}
			echo "</ul>";
		}            
	}
	echo '</tr><table>';
}