<?php

namespace App\Filament\Resources\DoctorScheduleResource\Pages;

use App\Filament\Resources\DoctorScheduleResource;
use App\Models\DoctorSchedule;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDoctorSchedule extends CreateRecord
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Return data pertama dari schedules dengan schedule_date
        if (isset($data['schedules']) && count($data['schedules']) > 0) {
            $firstSchedule = $data['schedules'][0];
            return [
                'doctor_id' => $firstSchedule['doctor_id'],
                'schedule_date' => $data['schedule_date'],
                'start_time' => $firstSchedule['start_time'],
                'end_time' => $firstSchedule['end_time'],
                'is_active' => $firstSchedule['is_active'] ?? true,
                'order' => $firstSchedule['order'] ?? 0,
                'notes' => $firstSchedule['notes'] ?? null,
            ];
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Ambil data form
        $formData = $this->form->getState();

        // Create additional schedules (skip first one as it's already created)
        if (isset($formData['schedules']) && count($formData['schedules']) > 1) {
            $schedules = array_slice($formData['schedules'], 1);

            foreach ($schedules as $schedule) {
                DoctorSchedule::create([
                    'doctor_id' => $schedule['doctor_id'],
                    'schedule_date' => $formData['schedule_date'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'is_active' => $schedule['is_active'] ?? true,
                    'order' => $schedule['order'] ?? 0,
                    'notes' => $schedule['notes'] ?? null,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
