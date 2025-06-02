<?php

require_once './config.php';

if (!isset($_GET['k']) || $_GET['k'] !== $key) {
  exit();
}

require_once './app/db.php';
require_once './functions.php';

$webhookUrl = $hostB24.$hookMsg.'/im.notify';

sendNotifyB24($webhookUrl, $taskShowForId, 'Ссылка на таблицу депримирования:  ' . $hostOfHosting.'/results.php?k='.$key);
exit();