1. Создать входящий webhook
2. Настроить конфигурацию



<h1>config.php</h1>

$key = '';

$configDB = [
	'host' => '127.0.0.1:3306',
	'db_name' => '',
	'db_user' => '',
	'db_pass' => '',
];

$downloadPath = 'HOST/tables/';
$hostB24 = 'HOSTB24';
$hookTask = '';
$url = $hostB24.$hookTask;
$hookMsg = '';
$tasksUri = '/tasks.task.list?order[ID]=asc';
$taskShowForId = 114;
$fixPrice = 100;
