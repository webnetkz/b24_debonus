<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './config.php';

if (!isset($_GET['k']) || $_GET['k'] !== $key) {
  exit();
}

require_once './app/db.php';
require_once './functions.php';

$webhookUrl = $hostB24.$hookMsg.'/im.notify';

$page = 0;
$baseUrl = $url;

while (true) {
	$url = $baseUrl . '/tasks.task.list.json?start=' . $page;
	$tasks = getTasksB24($url);
	$tasks = json_decode($tasks);

	if (empty($tasks->result->tasks)) {
		exit('END');
	}


	foreach($tasks->result->tasks as $k => $v) {
		$stmt = $db->query("SELECT `b_id`, `responsible_id` FROM `tasks` WHERE `b_id` = '" . $v->id . "'");
		$result = $stmt->fetch();

    preg_match('/!(.*?)!/', $v->title, $matches);
    if (@$matches[1]) {
      $price = $matches[1];
    } else {
      $price = $fixPrice;
    }
		
	  if (!$result && $v->subStatus == '-1') {
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

    if ($result && $v->subStatus == '-1') {
		  $price = (int)$price;


      $SQL = "UPDATE tasks SET price = price + {$price} WHERE b_id = '".$result['b_id']."'";
      $db->query($SQL);	  
      sendNotifyB24($webhookUrl, $result['responsible_id'], 'Вас повторно депремировали, детали в задаче: '.	$linkTask = $hostB24.'/company/personal/user/'.$result['responsible_id'].'/tasks/task/view/'.$result['b_id'].'/');
    }
	}

	$page += 50;
}