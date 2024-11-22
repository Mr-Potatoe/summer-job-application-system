<?php
include('config/database.php');

// Fetch all available jobs
$stmt = $pdo->query("SELECT * FROM jobs WHERE application_deadline >= CURDATE() ORDER BY created_at DESC");
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(45deg, #6f42c1, #007bff);
            color: white;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Custom Styles */
        .job-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
            color: #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .job-card:hover {
            transform: translateY(-5px);
        }

        .view-btn,
        .apply-btn {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
            width: 100%;
        }

        .view-btn {
            background-color: #007bff;
            color: white;
            text-decoration: none;
        }

        .apply-btn {
            background-color: #28a745;
            color: white;
            text-decoration: none;
            margin-top: 10px;
        }

        .view-btn:hover,
        .apply-btn:hover {
            opacity: 0.9;
        }

        .job-card h3 {
            color: #333;
            font-size: 1.25rem;
            margin-bottom: 15px;
        }

        .alert {
            font-size: 1.125rem;
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Make the container full-width and center content */
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Responsive Design */
        @media (max-width: 767px) {
            .view-btn, .apply-btn {
                width: auto;
                display: inline-block;
                padding: 12px 18px;
            }

            .job-card {
                margin-bottom: 15px;
            }
        }

    </style>
</head>

<body>

    <!-- Available Jobs Section -->
    <div class="container my-5" id="job-list">
        <h2 class="text-center mb-4 text-white">Available Summer Jobs</h2>

        <?php if (empty($jobs)): ?>
            <div class="alert alert-info text-center" role="alert">
                No available job positions at the moment. Please check back later.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($jobs as $job): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="job-card">
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p><strong>Qualifications:</strong> <?php echo htmlspecialchars($job['qualifications']); ?></p>
                            <p><strong>Deadline:</strong> <?php echo date('F j, Y', strtotime($job['application_deadline'])); ?></p>
                            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                            <a href="jobs/view.php?job_id=<?php echo $job['job_id']; ?>" class="view-btn">View Details</a>
                            <a href="jobs/apply.php?job_id=<?php echo $job['job_id']; ?>" class="apply-btn">Apply Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>

</html>
