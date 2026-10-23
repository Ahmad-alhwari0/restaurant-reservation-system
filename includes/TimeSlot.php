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
}
