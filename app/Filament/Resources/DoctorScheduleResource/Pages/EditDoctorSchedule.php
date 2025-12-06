<?php

namespace App\Filament\Resources\DoctorScheduleResource\Pages;

use App\Filament\Resources\DoctorScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorSchedule extends EditRecord
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Konversi data single schedule ke format repeater
        $data['schedules'] = [
            [
                'doctor_id' => $data['doctor_id'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'is_active' => $data['is_active'],
                'order' => $data['order'],
                'notes' => $data['notes'],
            ]
        ];
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ambil data pertama dari schedules untuk update record ini
        if (isset($data['schedules']) && count($data['schedules']) > 0) {
            return array_merge($data['schedules'][0], [
                'schedule_date' => $data['schedule_date'],
            ]);
        }
        
        return $data;
    }
}
