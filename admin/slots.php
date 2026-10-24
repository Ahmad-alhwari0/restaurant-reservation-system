<?php
require_once '../includes/init.php';
/** @var PDO $pdo */

Auth::requireAdmin('../');
$timeSlotModel = new TimeSlot($pdo);
$errors = [];
$success = '';

if (isset($_GET['saved'])) {
    $success = 'Time slot saved.';
}
if (isset($_GET['deleted'])) {
    $success = 'Time slot deleted.';
}

$editId  = (int) ($_GET['edit'] ?? 0);
$editing = $editId ? $timeSlotModel->getById($editId) : false;

$slotId   = $editing['id'] ?? 0;
$date     = $editing['slot_date'] ?? '';
$start    = $editing['start_time'] ?? '';
$end      = $editing['end_time'] ?? '';
$capacity = $editing['capacity'] ?? '';

// ---- 2. Form submitted ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $slotId = (int) ($_POST['slot_id'] ?? 0);

    if ($action === 'delete') {
        $result = $timeSlotModel->delete($slotId);
        if ($result === true) {
            header('Location: slots.php?deleted=1');
            exit;
        }
        $errors[] = $result;
        $slotId = 0;
    } else {
        $date     = trim($_POST['slot_date'] ?? '');
        $start    = $_POST['start_time'] ?? '';
        $end      = $_POST['end_time'] ?? '';
        $capacity = $_POST['capacity'] ?? '';

        $errors = $timeSlotModel->validate($date, $start, $end, $capacity);

        if (empty($errors)) {
            if ($slotId) {
                $result = $timeSlotModel->update($slotId, $date, $start, $end, $capacity);
            } else {
                $result = $timeSlotModel->create($date, $start, $end, $capacity);
            }

            if ($result === true) {
                header('Location: slots.php?saved=1');
                exit;
            }
            $errors[] = $result;
        }
    }
}

// ---- 3. Table ----
$slots = $timeSlotModel->getAll();
$pageTitle = 'Manage Time Slots';
$basePath  = '../';
require_once '../includes/header.php';
?>

<h1 class="h3 mb-4">Manage Time Slots</h1>

<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h2 class="h5 mb-3"><?= $slotId ? 'Edit Time Slot' : 'Add Time Slot' ?></h2>
        <form method="post" action="slots.php" class="row g-2 align-items-end" novalidate>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="slot_id" value="<?= (int) $slotId ?>">
            <div class="col-12 col-md-3">
                <label class="form-label small">Date</label>
                <input type="date" name="slot_date" class="form-control" value="<?= htmlspecialchars($date) ?>">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Start</label>
                <input type="time" name="start_time" class="form-control" value="<?= htmlspecialchars($start) ?>">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">End</label>
                <input type="time" name="end_time" class="form-control" value="<?= htmlspecialchars($end) ?>">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Capacity</label>
                <input type="number" name="capacity" min="1" class="form-control" value="<?= htmlspecialchars((string) $capacity) ?>">
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark flex-grow-1"><?= $slotId ? 'Update' : 'Add' ?></button>
                <?php if ($slotId): ?>
                    <a href="slots.php" class="btn btn-outline-secondary">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if (empty($slots)): ?>
    <div class="alert alert-info">No time slots yet. Add one above.</div>
<?php else: ?>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Capacity</th>
                        <th>Booked</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $slot): ?>
                        <tr>
                            <td class="text-nowrap"><?= date('D, M j, Y', strtotime($slot['slot_date'])) ?></td>
                            <td class="text-nowrap">
                                <?= date('g:i A', strtotime($slot['start_time'])) ?> – <?= date('g:i A', strtotime($slot['end_time'])) ?>
                            </td>
                            <td><?= (int) $slot['capacity'] ?></td>
                            <td>
                                <span class="badge <?= $slot['booked'] > 0 ? 'text-bg-warning' : 'text-bg-light border' ?>">
                                    <?= (int) $slot['booked'] ?>
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="slots.php?edit=<?= (int) $slot['id'] ?>" class="btn btn-sm btn-outline-dark">Edit</a>
                                <form method="post" action="slots.php" class="d-inline"
                                    onsubmit="return confirm('Delete this time slot?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="slot_id" value="<?= (int) $slot['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        <?= $slot['booked'] > 0 ? 'disabled title="Has active reservations"' : '' ?>>Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>