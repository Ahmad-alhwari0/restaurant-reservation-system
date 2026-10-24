<?php


class TimeSlot
{
    private $pdo;


    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByDate($date)
    {
        $stmt = $this->pdo->prepare("SELECT ts.id, ts.slot_date, ts.start_time, ts.end_time, ts.capacity,
        ts.capacity - COUNT(r.id) AS remaining FROM time_slots ts LEFT JOIN reservations r ON r.slot_id=ts.id AND r.status IN ('pending', 'confirmed') WHERE ts.slot_date = ?
        GROUP BY ts.id
        ORDER BY ts.start_time ");

        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT ts.id, ts.slot_date, ts.start_time, ts.end_time, ts.capacity,
        ts.capacity - COUNT(r.id) AS remaining FROM time_slots ts LEFT JOIN reservations r ON r.slot_id=ts.id AND r.status IN ('pending', 'confirmed') WHERE ts.id = ?
        GROUP BY ts.id
        ORDER BY ts.start_time ");

        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT ts.id, ts.slot_date, ts.start_time, ts.end_time, ts.capacity,
                                      COUNT(r.id) AS booked
                               FROM time_slots ts
                               LEFT JOIN reservations r
                                      ON r.slot_id = ts.id AND r.status IN ('pending', 'confirmed')
                               GROUP BY ts.id
                               ORDER BY ts.slot_date DESC, ts.start_time");
        return $stmt->fetchAll();
    }
    public function validate($date, $start, $end, $capacity)
    {
        $errors = [];

        if ($date === '' || !strtotime($date)) {
            $errors[] = 'Please enter a valid date.';
        }

        if ($start === '' || $end === '') {
            $errors[] = 'Start and end times are required.';
        } elseif ($end <= $start) {
            $errors[] = 'End time must be after start time.';
        }

        if (!filter_var($capacity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            $errors[] = 'Capacity must be a whole number (at least 1).';
        }

        return $errors;
    }
    public function create($date, $start, $end, $capacity)
    {
        $stmt = $this->pdo->prepare("INSERT INTO time_slots (slot_date, start_time, end_time, capacity) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$date, $start, $end, (int) $capacity]);
    }
    public function update($id, $date, $start, $end, $capacity)
    {
        $slot = $this->getById($id);
        if (!$slot) {
            return 'Time slot not found.';
        }

        $booked = $slot['capacity'] - $slot['remaining'];
        if ((int) $capacity < $booked) {
            return "Capacity cannot be less than the $booked active reservations.";
        }

        $stmt = $this->pdo->prepare("UPDATE time_slots SET slot_date = ?, start_time = ?, end_time = ?, capacity = ? WHERE id = ?");
        $stmt->execute([$date, $start, $end, (int) $capacity, $id]);
        return true;
    }
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM reservations WHERE slot_id = ? AND status IN ('pending', 'confirmed')");
        $stmt->execute([$id]);

        if ($stmt->fetchColumn() > 0) {
            return 'Cannot delete a time slot that has pending or confirmed reservations.';
        }

        $stmt = $this->pdo->prepare("DELETE FROM time_slots WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0 ? true : 'Time slot not found.';
    }
}
