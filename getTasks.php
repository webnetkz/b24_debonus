<?php

require_once './config.php';

if (!isset($_GET['k']) || $_GET['k'] !== $key) {
  exit();
}

require_once './app/db.php';
require_once './functions.php';


$lastPage = $db->query('SELECT last_page FROM `settings`');
$lastPage = $lastPage->fetch(PDO::FETCH_ASSOC);
$page = $lastPage['last_page'];

$baseUrl = $url;

while (true) {
	$url = $baseUrl . '/tasks.task.list.json?start=' . $page;
	$tasks = getTasksB24($url);
	$tasks = json_decode($tasks);

	if (empty($tasks->result->tasks)) {
		$query = "UPDATE `settings`
					SET last_page = :last_page
					WHERE id = 1";

		$stmt = $db->prepare($query);
		$stmt->execute([
			':last_page' => $page
		]);

		exit();
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
			$dataOfTask = [];
		}
	  }
	}


	$page += 50;
}