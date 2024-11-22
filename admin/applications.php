<?php
session_start();
include('../config/database.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Make sure to require the PHPMailer autoloader

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];
    $status = $_GET['status'];

    // Update the application status
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE application_id = ?");
    $stmt->execute([$status, $application_id]);

    // Fetch applicant's email, photo, and job details
    $stmt = $pdo->prepare("SELECT a.email, a.full_name, a.photo_path, j.title, j.description 
                           FROM applications a 
                           JOIN jobs j ON a.job_id = j.job_id
                           WHERE a.application_id = ?");
    $stmt->execute([$application_id]);
    $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($applicant) {
        // Prepare notification message
        $message = "Dear " . $applicant['full_name'] . ",\n\nYour application for the job of '" . $applicant['title'] . "' has been $status. Thank you for applying!";

        // Send email notification using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Set the SMTP server to Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'your@gmail.com';  // Your Gmail address
            $mail->Password = 'yourpassword';  // Your Gmail password (or app-specific password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('your@gmail.com', 'Job Application System');
            $mail->addAddress($applicant['email']);  // Applicant's email address

            // Content
            $mail->isHTML(false);
            $mail->Subject = 'Application Status Update';
            $mail->Body = $message;

            // Send email
            $mail->send();

            // Set session variable to indicate success
            $_SESSION['modal_message'] = "Application status updated and email notification sent!";
            $_SESSION['modal_type'] = "success";  // Could be "success" or "error"
        } catch (Exception $e) {
            // Error Modal Notification
            $_SESSION['modal_message'] = "Failed to send email. Mailer Error: " . htmlspecialchars($mail->ErrorInfo);
            $_SESSION['modal_type'] = "error";  // Could be "error"
        }

        // Insert notification into the database
        $stmt = $pdo->prepare("INSERT INTO notifications (application_id, message) VALUES (?, ?)");
        $stmt->execute([$application_id, "Your application for the job of " . $applicant['title'] . " has been $status."]);
    } else {
        // Applicant Not Found Error Modal
        $_SESSION['modal_message'] = "Applicant not found.";
        $_SESSION['modal_type'] = "error";  // Could be "error"
    }

    // Redirect to avoid resubmission
    header("Location: applications.php");
    exit();
}

// Fetch all applications with job details and photo_path
$stmt = $pdo->query("SELECT a.application_id, a.full_name, a.status, a.photo_path, j.title, j.description 
                     FROM applications a 
                     JOIN jobs j ON a.job_id = j.job_id");
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Manage Applications Section -->
<?php include 'includes/head.php'; ?>

<!-- Include the Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Manage Applications Section -->
<section class="container mt-5">
    <h1 class="mb-4">Manage Applications</h1>

    <h2 class="mb-4">All Applications</h2>

    <!-- Check if applications exist -->
    <?php if (empty($applications)): ?>
        <div class="alert alert-info" role="alert">
            No applications available at the moment.
        </div>
    <?php else: ?>
        <!-- Application List -->
        <div class="row">
            <?php foreach ($applications as $application): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <img src="../uploads/<?php echo htmlspecialchars($application['photo_path']); ?>" alt="Applicant Photo" class="card-img-top" style="width: 100px; height: 100px; object-fit: cover; margin: 10px auto;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($application['full_name']); ?></h5>
                            <p class="card-text"><strong>Job Title:</strong> <?php echo htmlspecialchars($application['title']); ?></p>
                            <p class="card-text"><strong>Job Description:</strong> <?php echo htmlspecialchars($application['description']); ?></p>
                            <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($application['status']); ?></p>
                        </div>
                        <div class="card-footer text-center">
                            <!-- Update Status Links -->
                            <a href="applications.php?application_id=<?php echo $application['application_id']; ?>&status=Accepted" class="btn btn-success btn-sm mx-2">Accept</a>
                            <a href="applications.php?application_id=<?php echo $application['application_id']; ?>&status=Rejected" class="btn btn-danger btn-sm mx-2">Reject</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>

<!-- Bootstrap Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Application Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        <?php if (isset($_SESSION['modal_message'])): ?>
            // Set the modal message content
            document.getElementById("modalMessage").innerHTML = "<?php echo $_SESSION['modal_message']; ?>";

            // Show the modal
            var myModal = new bootstrap.Modal(document.getElementById("statusModal"));
            myModal.show();

            // Clear session message after modal is shown
            <?php unset($_SESSION['modal_message']); unset($_SESSION['modal_type']); ?>
        <?php endif; ?>
    };
</script>

<?php include 'includes/footer.php'; ?>
