<?php

namespace App\Factories\SaleDepartment;

use App\DTO\SaleDepartment\ReportDTO;
use App\Models\Department;
use App\Models\User;
use App\Services\SaleDepartmentServices\ReportBuilder;
use App\Services\UserServices\UserService;
use App\Services\SaleDepartmentServices\WorkPlanService;
use Carbon\Carbon;

class ReportFactory
{
    private ReportBuilder $builder;

    public function __construct()
    {
        $this->builder = new ReportBuilder(
            new UserService(),
            new WorkPlanService()
        );
    }

    public function createFullReport(Carbon $date, ?Department $department = null): ReportDTO
    {
        return $this->builder->buildFullReport($date, $department);
    }

    public function createUserReport(Carbon $date, User $user): ReportDTO
    {
        return $this->builder->buildUserReport($date, $user);
    }

    public function createUserSubReport(ReportDTO $mainReport, User $user): ?ReportDTO
    {
        return $this->builder->getUserSubdata($mainReport, $user);
    }
}
