<?php
// delete.php
session_start();
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get employee photo before deletion
    $sql = "SELECT photo FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    
    // Delete employee record
    $sql = "DELETE FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Delete employee photo
        if (!empty($employee['photo'])) {
            unlink("uploads/" . $employee['photo']);
        }
    }
}

header("Location: index.php");
exit();
?>