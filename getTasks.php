<?php

require_once './config.php';

if (!isset($_GET['k']) || $_GET['k'] !== $key) {
  exit();
}

require_once './app/db.php';
require_once './functions.php';

$webhookUrl = $hostB24.$hookMsg.'/im.notify';

// $lastPage = $db->query('SELECT last_page FROM `settings`');
// $lastPage = $lastPage->fetch(PDO::FETCH_ASSOC);
// $page = $lastPage['last_page'];

$page = 0;
$baseUrl = $url;

while (true) {
	$url = $baseUrl . '/tasks.task.list.json?start=' . $page;
	$tasks = getTasksB24($url);
	$tasks = json_decode($tasks);

	if (empty($tasks->result->tasks)) {
		// $query = "UPDATE `settings`
		// 			SET last_page = :last_page
		// 			WHERE id = 1";

		// $stmt = $db->prepare($query);
		// $stmt->execute([
		// 	':last_page' => $page
		// ]);

		exit('END');
	}


	foreach($tasks->result->tasks as $k => $v) {
		$stmt = $db->query("SELECT `b_id` FROM `tasks` WHERE `b_id` = '" . $v->id . "'");
		$result = $stmt->fetch();

	  if (!$result && $v->subStatus == '-1') {
		preg_match('/!(.*?)!/', $v->title, $matches);

		if (@$matches[1]) {
		  $price = $matches[1];
		} else {
		  $price = $fixPrice;
		}

		$linkTask = $hostB24.'/company/personal/user/'.$taskShowForId.'/tasks/task/view/'.$v->id.'/';
		$month = (new DateTime($v->deadline))->format('n');
		$dataOfTask = [];

		$dataOfTask = [
		  'b_id' => $v->id,
		  'title' => $v->title,
		  'price' => $price,
		  'deadline' => $v->deadline,
		  'responsible' => $v->responsible->name,
		  'creator' => $v->creator->name,
		  'link' => $linkTask,
		  'month' => $month
		];

		if (!empty($dataOfTask)) {
			insertTaskDB($dataOfTask, $db);
      sendNotifyB24($webhookUrl, $v->responsible->id, 'Вас депремировали, детали в задаче: '.	$linkTask = $hostB24.'/company/personal/user/'.$v->responsible->id.'/tasks/task/view/'.$v->id.'/');
			$dataOfTask = [];
		}
	  }
	}


	$page += 50;
}