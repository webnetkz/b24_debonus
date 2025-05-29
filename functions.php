<?php


function sendNotifyB24($webhookUrl, $id, $mess) {  
  $params = [
      'to' => $id,
      'message' => $mess,
      'type' => 'SYSTEM'
  ];
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $webhookUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($ch, CURLOPT_POST, true);
  
  $response = curl_exec($ch);
  curl_close($ch);
  
  echo $response;
}


function getTasksB24($url) {
  $params = [
      'order' => ['ID' => 'asc'],
      'filter' => [],
      'select' => ['ID', 'TITLE', 'STATUS', 'RESPONSIBLE_ID', 'DEADLINE', 'CREATED_BY']
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($ch, CURLOPT_POST, 1);

  $response = curl_exec($ch);
  curl_close($ch);

	return $response;
}


function insertTaskDB($data, $db) {
  $query = "INSERT INTO tasks
  (b_id, title, price, deadline, creator, responsible, link, month)
  VALUES
  (:b_id, :title, :price, :deadline, :creator, :responsible, :link, :month)";

  $stmt = $db->prepare($query);
  $stmt->execute([
      ':b_id' => $data['b_id'],
      ':title' => $data['title'],
      ':price' => $data['price'],
      ':deadline' => $data['deadline'],
      ':creator' => $data['creator'],
      ':responsible' => $data['responsible'],
      ':link' => $data['link'],
	  ':month' => $data['month']
  ]);
}