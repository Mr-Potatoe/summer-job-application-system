<?php
session_start();
include('../config/database.php');

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Display job count and application count
$jobStmt = $pdo->query("SELECT COUNT(*) FROM jobs");
$jobCount = $jobStmt->fetchColumn();

$appStmt = $pdo->query("SELECT COUNT(*) FROM applications");
$appCount = $appStmt->fetchColumn();
?>

<?php include 'includes/head.php'; ?>


    <!-- Include the Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <section class="container mt-5">
        <h1 class="mb-4">Welcome to the Admin Dashboard</h1>

        <div class="row">
            <!-- Job and Application Counts -->
            <div class="col-md-6">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Jobs</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $jobCount; ?></h5>
                        <p class="card-text">Manage and view available job listings.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Applications</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $appCount; ?></h5>
                        <p class="card-text">View and manage submitted job applications.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


<?php include 'includes/footer.php'; ?>