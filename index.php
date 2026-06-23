<?php
require 'db.php';

$id = '';
$name = '';
$email = '';
$update_mode = false;

// ==========================================
// 1. CREATE & UPDATE Logic
// ==========================================
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    if ($_POST['id'] != '') {
        // UPDATE existing record
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $id]);
    } else {
        // CREATE new record
        $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);
    }
    header('location: index.php');
}

// ==========================================
// 2. DELETE Logic
// ==========================================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header('location: index.php');
}

// ==========================================
// 3. FETCH Data for Editing (Pre-fill form)
// ==========================================
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update_mode = true;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $name = $user['name'];
        $email = $user['email'];
    }
}

// ==========================================
// 4. READ Logic (Fetch all users for the table)
// ==========================================
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP HTML MySQL CRUD</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        form { margin-bottom: 30px; padding: 15px; border: 1px solid #ccc; width: 300px; }
        input { display: block; width: 93%; margin-bottom: 10px; padding: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 6px 10px; text-decoration: none; border-radius: 3px; color: white; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        .btn-save { background-color: #28a745; border: none; color: white; width: 100%; cursor: pointer;}
    </style>
</head>
<body>

    <h2>User Management (CRUD)</h2>

    <form action="index.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        
        <button type="submit" name="save" class="btn btn-save">
            <?php echo $update_mode ? "Update User" : "Add User"; ?>
        </button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <a href="index.php?edit=<?php echo $user['id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="index.php?delete=<?php echo $user['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>