<?php

require_once './config.php';

if (!isset($_GET['k']) || $_GET['k'] !== $key) {
  exit();
}

require_once './app/db.php';
require_once './functions.php';

$webhookUrl = $hostB24.$hookMsg.'/im.notify';
$url = $url.'/tasks.task.list.json?start=100';

$response = $db->query('SELECT * FROM `tasks`');
$tasks = $response->fetchAll();

$tableName = 'debonus'.date('d-n-Y').'.csv';
$csvFile = fopen('./tables/'.$tableName, 'w');

// Добавляем BOM для корректного отображения UTF-8 в Excel
fwrite($csvFile, "\xEF\xBB\xBF");

fputcsv($csvFile, ['ID', 'Название', 'Морали', 'Дедлайн', 'Ответственный', 'Создатель', 'Ссылка'], ',', '"', '\\');

foreach ($tasks as $k => $v) {
	
	if ($v['month'] == date('n')) {
		  // Приводим строки к UTF-8, если есть сомнения
		  $title = mb_convert_encoding($v['title'], 'UTF-8', 'auto');
		  $responsibleName = isset($v['responsible']['name']) ? mb_convert_encoding($v['responsible']['name'], 'UTF-8', 'auto') : '';
		  $creatorName = isset($v['creator']['name']) ? mb_convert_encoding($v['creator']['name'], 'UTF-8', 'auto') : '';

		  fputcsv($csvFile, [
			$v['id'],
			$title,
			$v['price'],
			$v['deadline'],
			$responsibleName,
			$creatorName,
			$v['link']
		  ], ',', '"', '\\');
	}
}

fclose($csvFile);

sendNotifyB24($webhookUrl, $taskShowForId, 'Ссылка на таблицу депримирования:  ' . $downloadPath.$tableName);
exit();