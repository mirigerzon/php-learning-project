<?php
include 'db.php';

if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $file_name = $_FILES['images']['name'][$key];
        $file_tmp = $_FILES['images']['tmp_name'][$key];

        $target_dir = "uploads/tasks/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0777, true);

        $target_file = $target_dir . time() . "_" . basename($file_name);

        if (move_uploaded_file($file_tmp, $target_file)) {
            $stmt = $conn->prepare("INSERT INTO task_images (task_id, image_path) VALUES (?, ?)");
            $stmt->bind_param("is", $task_id, $target_file);
            $stmt->execute();
        }
    }
    header("Location: task_view.php?id=$task_id");
    exit;
}
?>