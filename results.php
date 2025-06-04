<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './config.php';

session_start();

$referer = $_SERVER['HTTP_REFERER'] ?? '';

$fromLink = parse_url($referer);

if (@$fromLink['host'] == $hostB24) {
	$_SESSION['check'] = true;
}

if (!isset($_GET['k']) || $_GET['k'] !== $key) { //|| !$_SESSION['check']) {
  exit();
}


require_once './app/db.php';


$filters = [];
$params = [];

if (!empty($_GET['date'])) {
    $filters[] = 'DATE(deadline) = :date';
    $params[':date'] = $_GET['date'];
}

if (!empty($_GET['responsible'])) {
    $filters[] = 'responsible LIKE :responsible';
    $params[':responsible'] = '%' . $_GET['responsible'] . '%';
}

$where = '';
if ($filters) {
    $where = 'WHERE ' . implode(' AND ', $filters);
}

$sql = "SELECT * FROM tasks $where ORDER BY deadline DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll();

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


<div class="container mt-4">
    <form method="get" class="row mb-4">
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <input type="text" name="responsible" class="form-control" placeholder="Ответственный" value="<?= htmlspecialchars($_GET['responsible'] ?? '') ?>">
        </div>
		<input type="text" value="<?=$key;?>" name="k" style="display: none;">
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Фильтровать</button>
        </div>
        <div class="col-md-2">
            <a href="?k=<?=$key;?>" class="btn btn-secondary w-100">Сбросить</a>
        </div>
    </form>

    <table class="table table-striped table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Задача</th>
                <th>Дата</th>
                <th>Ответственный</th>
                <th>Постановщик</th>
                <th>Морали</th>
                <th>Ссылка на задачу</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tasks as $v): ?>
                <tr>
                    <td><?= $v['b_id'] ?></td>
                    <td><?= $v['title'] ?></td>
                    <td><?= $v['deadline'] ?></td>
                    <td><?= $v['responsible'] ?></td>
                    <td><?= $v['creator'] ?></td>
                    <td><?= $v['price'] ?></td>
                    <td><a href="<?= $v['link'] ?>" target="_blank">Открыть задачу</a></td>
                    <td>
                      <i class="bi bi-trash"></i>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
	
	<div class="col-md-2">
		<button type="button" onclick="printTable()" class="btn btn-success w-100">Печать</button>
	</div>
	
	<script>
		function printTable() {
			const tableHtml = document.querySelector('table').outerHTML;
			const style = `
				<style>
					table { border-collapse: collapse; width: 100%; }
					th, td { border: 1px solid #000; padding: 8px; }
					th { background: #f8f9fa; }
				</style>
			`;
			const win = window.open('', '', 'height=700,width=900');
			win.document.write('<html><head><title>Печать таблицы</title>');
			win.document.write(style);
			win.document.write('</head><body>');
			win.document.write(tableHtml);
			win.document.write('</body></html>');
			win.document.close();
			win.print();
		}
	</script>


</div>
