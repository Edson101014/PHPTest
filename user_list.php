<?php
include 'db/db_con.php';

try {
    $xqry = "SELECT * FROM users";
    $stmt = $pdo->query($xqry);
    $stmt_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <?php 
    include("navbar.php");
    ?>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Registered Users</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Middle Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Profile Picture</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($stmt_user) > 0){
                 foreach ($stmt_user as $user): ?>
                    <tr>
                        <td><?php echo $user['first_name']; ?></td>
                        <td><?php echo $user['last_name']; ?></td>
                        <td><?php echo $user['middle_name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['phone_number']; ?></td>
                        <td>
                            <img src="uploads/<?php echo $user['profile_image']; ?>" alt="Profile Picture" width="100" height="100" style="object-fit: cover;">
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php }else{ ?>
                <tr>
                    <td colspan="6" class="text-center">No users found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
