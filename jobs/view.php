<?php
include('../config/database.php');

// Check if job_id is set in the URL
if (!isset($_GET['job_id'])) {
    die('Job ID is missing.');
}

// Get job details
$job_id = $_GET['job_id'];
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE job_id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

// If job doesn't exist, show an error
if (!$job) {
    die('Job not found.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Job Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(45deg, #6f42c1, #007bff);
            color: white;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .job-details {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .job-title {
            font-size: 2rem;
            font-weight: 600;
            color: #007bff;
        }

        .job-apply-btn {
            display: inline-block;
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            font-size: 1.1rem;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        .job-apply-btn:hover {
            background-color: #218838;
        }

        .job-description {
            margin-top: 20px;
        }

        .job-details h5 {
            font-size: 1.1rem;
            font-weight: 500;
            color: #333;
        }

        .job-details p {
            color: #666;
            font-size: 1rem;
        }

        /* Style the qualification section to differentiate it */
        .job-details .mt-3 h5,
        .job-details .job-description h5 {
            color: #6f42c1;
        }

        /* Media Queries for Mobile Responsiveness */
        @media (max-width: 767px) {
            .job-details {
                padding: 20px;
            }

            .job-title {
                font-size: 1.75rem;
            }

            .job-apply-btn {
                font-size: 1rem;
                padding: 10px 18px;
            }
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <div class="job-details">
            <h1 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h1>

            <div class="mt-3">
                <h5><strong>Qualifications:</strong></h5>
                <p><?php echo nl2br(htmlspecialchars($job['qualifications'])); ?></p>
            </div>

            <div class="job-description mt-4">
                <h5><strong>Description:</strong></h5>
                <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
            </div>

            <div class="mt-4">
                <h5><strong>Application Deadline:</strong> <?php echo date('F j, Y', strtotime($job['application_deadline'])); ?></h5>
            </div>

            <div class="mt-4">
                <a href="apply.php?job_id=<?php echo $job['job_id']; ?>" class="job-apply-btn">Apply Now</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>

</html>