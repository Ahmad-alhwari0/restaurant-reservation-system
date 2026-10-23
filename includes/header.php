<?php
$pageTitle = $pageTitle ?? 'Restaurant Reservations';
$basePath  = $basePath ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?= $basePath ?>index.php">🍽️ Restaurant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <?php if (Auth::isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>admin/index.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>admin/reservations.php">Reservations</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>admin/slots.php">Time Slots</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>admin/users.php">Users</a></li>
                    <?php elseif (Auth::isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>slots.php">Available Slots</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>my-reservations.php">My Reservations</a></li>
                    <?php endif; ?>

                    <?php if (Auth::isLoggedIn()): ?>
                        <li class="nav-item">
                            <span class="navbar-text mx-lg-3">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm" href="<?= $basePath ?>logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>register.php">Register</a></li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">