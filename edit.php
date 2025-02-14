<?php
// edit.php
session_start();
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE employees SET name=?, email=?, password=?, mobile=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $password, $mobile, $id);
    } else {
        $sql = "UPDATE employees SET name=?, email=?, mobile=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $mobile, $id);
    }
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        $photo = uniqid() . '_' . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo;
        
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            // Delete old photo
            if (!empty($employee['photo'])) {
                unlink($target_dir . $employee['photo']);
            }
            
            $sql = "UPDATE employees SET photo=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $photo, $id);
            $stmt->execute();
        }
    }
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Employee</h1>
        <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Password (leave blank to keep current):</label>
                <input type="password" name="password">
            </div>
            
            <div class="form-group">
                <label>Current Photo:</label>
                <img src="uploads/<?php echo $employee['photo']; ?>" alt="Employee photo" class="employee-img">
                <input type="file" name="photo" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Mobile:</label>
                <input type="tel" name="mobile" value="<?php echo htmlspecialchars($employee['mobile']); ?>" required>
            </div>
            
            <button type="submit" class="submit-btn">Update Employee</button>
        </form>
    </div>
</body>
</html>