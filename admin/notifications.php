<?php
session_start();
include('../config/database.php');

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all notifications
$stmt = $pdo->query("SELECT n.notification_id, n.message, n.notified_at, a.full_name 
                     FROM notifications n 
                     JOIN applications a ON n.application_id = a.application_id
                     ORDER BY n.notified_at DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle notification deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_notification'])) {
    $notification_id = $_POST['notification_id'];

    // Delete the notification from the database
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE notification_id = ?");
    $stmt->execute([$notification_id]);
    echo "Notification deleted successfully!";
}
?>
<?php include 'includes/head.php'; ?>

<!-- Include the Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Manage Notifications Section -->
<section class="container mt-5">
    <h1 class="mb-4">Manage Notifications</h1>

    <h2 class="mb-4">All Notifications</h2>

    <!-- Check if notifications are available -->
    <?php if (empty($notifications)): ?>
        <p>No notifications available.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($notifications as $notification): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><strong>Applicant: <?php echo htmlspecialchars($notification['full_name']); ?></strong></h5>
                            <p class="card-text"><strong>Message:</strong> <?php echo htmlspecialchars($notification['message']); ?></p>
                            <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars($notification['notified_at']); ?></p>
                        </div>
                        <div class="card-footer text-center">
                            <!-- Delete Notification Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                <button type="submit" name="delete_notification" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this notification?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Back to Dashboard Link -->
    <a href="index.php" class="btn btn-primary my-4">Back to Dashboard</a>
</section>

<?php include 'includes/footer.php'; ?>
