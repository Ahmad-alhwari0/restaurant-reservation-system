<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Reservations</title> <!-- PHP: page title -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="index.php">🍽️ Restaurant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <!-- PHP: show ONLY if logged in as ADMIN -->
                    <li class="nav-item"><a class="nav-link" href="admin/index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/reservations.php">Reservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/slots.php">Time Slots</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/users.php">Users</a></li>

                    <!-- PHP: show ONLY if logged in as normal USER -->
                    <li class="nav-item"><a class="nav-link" href="slots.php">Available Slots</a></li>
                    <li class="nav-item"><a class="nav-link" href="my-reservations.php">My Reservations</a></li>

                    <!-- PHP: show ONLY if logged in (any role) -->
                    <li class="nav-item">
                        <span class="navbar-text mx-lg-3">Hi, User</span> <!-- PHP: user's name -->
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
                    </li>

                    <!-- PHP: show ONLY if NOT logged in -->
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>

                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">