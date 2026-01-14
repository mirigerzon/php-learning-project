<?php
include 'db.php';

if (isset($_POST['image_id']) && isset($_POST['task_id'])) {
    $image_id = $_POST['image_id'];
    $task_id = $_POST['task_id'];

    $res = $conn->query("SELECT image_path FROM task_images WHERE id=$image_id");
    $row = $res->fetch_assoc();
    if ($row) {
        if (file_exists($row['image_path']))
            unlink($row['image_path']);
        $conn->query("DELETE FROM task_images WHERE id=$image_id");
    }

    header("Location: task_view.php?id=$task_id");
    exit;
}
?>