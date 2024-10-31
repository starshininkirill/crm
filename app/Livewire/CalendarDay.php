<?php

namespace App\Livewire;

use App\Models\WorkingDay;
use Livewire\Component;

class CalendarDay extends Component
{
    public $day;

    public function toggleType(){
        $updatedDay = WorkingDay::updateOrCreate(
            ['date' => $this->day['date']->format('Y-m-d')],
            ['is_working_day' => !$this->day['is_workday']]
        );
        

        $this->day['is_workday'] = $updatedDay->isWorkingDay();
    }

    public function render()
    {
        return view('livewire.calendar-day');
    }
}
