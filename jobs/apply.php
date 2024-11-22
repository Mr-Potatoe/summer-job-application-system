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

// Handle application form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $contact_number = $_POST['contact_number'];
    $experience = $_POST['experience'];
    $email = $_POST['email'];

    // Handle file upload (applicant's photo)
    $photo_path = '';
    if ($_FILES['photo']['error'] == 0) {
        $photo_path = '../uploads/' . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
    }

    // Insert the application into the database
    $stmt = $pdo->prepare("INSERT INTO applications (job_id, full_name, address, age, contact_number, experience, email, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$job_id, $full_name, $address, $age, $contact_number, $experience, $email, $photo_path]);

    // Set a flag to show the toast
    echo '<script>
    window.onload = function() {
        var toast = new bootstrap.Toast(document.getElementById("successToast"));
        toast.show();
        setTimeout(function() {
            window.location = "../index.php";
        }, 3000); // Redirect after 3 seconds
    }
  </script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <style>
        body {
            background: linear-gradient(45deg, #6f42c1, #007bff);
            color: white;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Custom Form Styles */
        .apply-form {
            border: 1px solid #ddd;
            padding: 30px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        .apply-form h1 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: bold;
        }

        .apply-form button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            width: 100%;
            font-size: 18px;
            font-weight: bold;
        }

        .apply-form button:hover {
            opacity: 0.9;
        }

        .form-control,
        .form-control-file,
        textarea {
            margin-bottom: 15px;
        }

        .form-control::placeholder,
        textarea::placeholder {
            color: #888;
        }

        /* Button Styles */
        .form-control,
        .form-control-file,
        textarea {
            border-radius: 5px;
            padding: 10px;
        }

        /* Media Queries for Mobile Responsiveness */
        @media (max-width: 767px) {
            .apply-form {
                padding: 20px;
            }

            .apply-form h1 {
                font-size: 1.5rem;
            }

            .apply-form button {
                font-size: 16px;
                padding: 10px 15px;
            }
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <h1 class="text-center mb-4">Apply for <?php echo htmlspecialchars($job['title']); ?></h1>
        <div class="apply-form">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                </div>
                <div class="mb-3">
                    <textarea name="address" class="form-control" placeholder="Address" required></textarea>
                </div>
                <div class="mb-3">
                    <input type="number" name="age" class="form-control" placeholder="Age" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" required>
                </div>
                <div class="mb-3">
                    <textarea name="experience" class="form-control" placeholder="Experience (Optional)"></textarea>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="file" name="photo" class="form-control-file" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-success">Submit Application</button>
            </form>
        </div>
    </div>
    <!-- HTML for the toast -->
    <div class="toast-container position-absolute top-50 start-50 translate-middle p-3" style="z-index: 1050;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Application submitted successfully! Please wait for updates.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>



    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>

</html>