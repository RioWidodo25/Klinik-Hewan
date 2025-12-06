<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG DOCTOR SCHEDULES ===\n\n";

$schedules = \App\Models\DoctorSchedule::with('doctor')
    ->orderBy('day_of_week')
    ->orderBy('start_time')
    ->get();

echo "Total Schedules: " . $schedules->count() . "\n\n";

foreach ($schedules as $schedule) {
    echo sprintf(
        "ID: %d | Doctor: %s | Day: %s | Time: %s - %s | Active: %s\n",
        $schedule->id,
        $schedule->doctor->name,
        $schedule->day_of_week,
        $schedule->start_time->format('H:i'),
        $schedule->end_time->format('H:i'),
        $schedule->is_active ? 'Yes' : 'No'
    );
}

echo "\n=== GROUP BY DAY ===\n\n";

$byDay = $schedules->groupBy('day_of_week');

foreach ($byDay as $day => $daySchedules) {
    echo ucfirst($day) . " (" . $daySchedules->count() . " schedules):\n";
    foreach ($daySchedules as $s) {
        echo "  - " . $s->doctor->name . " (" . $s->start_time->format('H:i') . " - " . $s->end_time->format('H:i') . ")\n";
    }
    echo "\n";
}
