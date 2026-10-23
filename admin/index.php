<?php
require_once '../includes/init.php';
/** @var PDO $pdo */

Auth::requireAdmin('../');
$reservationModel = new Reservation($pdo);
$userModel=new User($pdo);
$counts=$reservationModel->countByStatus();
$totalReservations=array_sum($counts);
$usersCount=$userModel->countUsers();

$pageTitle = 'Admin Dashboard';
$basePath  = '../';
require_once '../includes/header.php';

$cards = [
    ['label' => 'Total Reservations', 'value' => $totalReservations,   'class' => 'text-bg-dark'],
    ['label' => 'Pending',            'value' => $counts['pending'],   'class' => 'text-bg-warning'],
    ['label' => 'Confirmed',          'value' => $counts['confirmed'], 'class' => 'text-bg-success'],
    ['label' => 'Users',              'value' => $usersCount,          'class' => 'text-bg-primary'],
];
?>

<h1 class="h3 mb-4">Dashboard</h1>

<div class="row g-3 mb-4">
    <?php foreach ($cards as $card): ?>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 <?= $card['class'] ?>">
                <div class="card-body">
                    <div class="small text-uppercase opacity-75"><?= $card['label'] ?></div>
                    <div class="display-6 fw-semibold"><?= (int) $card['value'] ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h2 class="h5 mb-3">Reservations by Status</h2>
        <div class="mx-auto" style="max-width: 420px;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const statusCounts = <?= json_encode($counts) ?>;

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Confirmed', 'Rejected', 'Cancelled'],
            datasets: [{
                data: [statusCounts.pending, statusCounts.confirmed, statusCounts.rejected, statusCounts.cancelled],
                backgroundColor: ['#ffc107', '#198754', '#dc3545', '#6c757d']
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>