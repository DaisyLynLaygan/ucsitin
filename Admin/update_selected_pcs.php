<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['pcs'], $_POST['mark'])) {
    $pcs = $_POST['pcs'];
    $status = $_POST['mark'];

    if (!empty($pcs)) {
        $placeholders = implode(',', array_fill(0, count($pcs), '?'));
        $types = str_repeat('i', count($pcs));
        $sql = "UPDATE pcs SET status = ? WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $params = array_merge([$status], $pcs);
            $bind_names[] = 's' . $types;

            foreach ($params as &$param) {
                $bind_names[] = &$param;
            }

            call_user_func_array([$stmt, 'bind_param'], $bind_names);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header("Location: lab-management.php?lab=" . urlencode($_POST['lab']));
exit();
?>
