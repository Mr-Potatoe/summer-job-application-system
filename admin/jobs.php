<?php
session_start();
include('../config/database.php');

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch the job to edit
if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE job_id = ?");
    $stmt->execute([$job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        echo "Job not found.";
        exit();
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Add a new job
    if (isset($_POST['add_job'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $qualifications = $_POST['qualifications'];
        $deadline = $_POST['application_deadline'];

        $stmt = $pdo->prepare("INSERT INTO jobs (title, description, qualifications, application_deadline) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $qualifications, $deadline]);
        $message = "Job added successfully!";
    }

    // Edit an existing job
    elseif (isset($_POST['edit_job'])) {
        $job_id = $_POST['job_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $qualifications = $_POST['qualifications'];
        $deadline = $_POST['application_deadline'];

        $stmt = $pdo->prepare("UPDATE jobs SET title = ?, description = ?, qualifications = ?, application_deadline = ?
                               WHERE job_id = ?");
        $stmt->execute([$title, $description, $qualifications, $deadline, $job_id]);
        $message = "Job updated successfully!";
        header('Location: jobs.php'); // Redirect after successful edit
        exit();
    }

}
    

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add a new job
    if (isset($_POST['add_job'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $qualifications = $_POST['qualifications'];
        $deadline = $_POST['application_deadline'];

        $stmt = $pdo->prepare("INSERT INTO jobs (title, description, qualifications, application_deadline) 
                                VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $qualifications, $deadline]);
        $message = "Job added successfully!";
    }

    // Edit an existing job
    if (isset($_POST['edit_job'])) {
        $job_id = $_POST['job_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $qualifications = $_POST['qualifications'];
        $deadline = $_POST['application_deadline'];

        $stmt = $pdo->prepare("UPDATE jobs SET title = ?, description = ?, qualifications = ?, application_deadline = ?
                               WHERE job_id = ?");
        $stmt->execute([$title, $description, $qualifications, $deadline, $job_id]);
        $message = "Job updated successfully!";
    }

    // Delete a job
    if (isset($_POST['delete_job'])) {
        $job_id = $_POST['job_id'];

        $stmt = $pdo->prepare("DELETE FROM jobs WHERE job_id = ?");
        $stmt->execute([$job_id]);
        $message = "Job deleted successfully!";
    }
}

// Fetch jobs for display
$stmt = $pdo->query("SELECT * FROM jobs");
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/head.php'; ?>

<!-- Include the Navbar -->
<?php include 'includes/navbar.php'; ?>

<section class="container my-5">
    <h1 class="mb-4">Manage Jobs</h1>

    <!-- Success or Error Messages -->
    <?php if (isset($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Button to trigger the Add Job modal -->
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addJobModal">
        Add New Job
    </button>

    <!-- Modal for adding a new job -->
    <div class="modal fade" id="addJobModal" tabindex="-1" aria-labelledby="addJobModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJobModalLabel">Add New Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Job Title</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="Job Title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Job Description</label>
                            <textarea id="description" name="description" class="form-control" placeholder="Job Description" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="qualifications" class="form-label">Qualifications</label>
                            <textarea id="qualifications" name="qualifications" class="form-control" placeholder="Qualifications" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="application_deadline" class="form-label">Application Deadline</label>
                            <input type="date" id="application_deadline" name="application_deadline" class="form-control" required>
                        </div>

                        <button type="submit" name="add_job" class="btn btn-success">Add Job</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Jobs -->
    <h2 class="mb-4">Current Jobs</h2>
    <?php if (empty($jobs)): ?>
        <div class="alert alert-info" role="alert">
            No jobs available at the moment.
        </div>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($jobs as $job): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($job['title']); ?></strong><br>
                        <p><?php echo htmlspecialchars($job['description']); ?></p>
                        <small><strong>Application Deadline:</strong> <?php echo $job['application_deadline']; ?></small>
                    </div>

                    <div>
                        <!-- Edit Job Button Trigger Modal -->
                        <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editJobModal<?php echo $job['job_id']; ?>">Edit</button>

                        <!-- Modal for Editing a Job -->
                        <div class="modal fade" id="editJobModal<?php echo $job['job_id']; ?>" tabindex="-1" aria-labelledby="editJobModalLabel<?php echo $job['job_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editJobModalLabel<?php echo $job['job_id']; ?>">Edit Job</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST">
                                            <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">

                                            <div class="mb-3">
                                                <label for="title" class="form-label">Job Title</label>
                                                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($job['title']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="description" class="form-label">Job Description</label>
                                                <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($job['description']); ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="qualifications" class="form-label">Qualifications</label>
                                                <textarea id="qualifications" name="qualifications" class="form-control" rows="4" required><?php echo htmlspecialchars($job['qualifications']); ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="application_deadline" class="form-label">Application Deadline</label>
                                                <input type="date" id="application_deadline" name="application_deadline" class="form-control" value="<?php echo $job['application_deadline']; ?>" required>
                                            </div>

                                            <button type="submit" name="edit_job" class="btn btn-success">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Job -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                            <button type="submit" name="delete_job" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job?')">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>


<?php include 'includes/footer.php'; ?>
