<?php

require_once './config.php';

if (!isset($_GET['k']) || $_GET['k'] !== $key) {
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

<div class="container mt-4">
    <form method="get" class="row mb-4">
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <input type="text" name="responsible" class="form-control" placeholder="Ответственный" value="<?= htmlspecialchars($_GET['responsible'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Фильтровать</button>
        </div>
        <div class="col-md-2">
            <a href="?" class="btn btn-secondary w-100">Сбросить</a>
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
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
