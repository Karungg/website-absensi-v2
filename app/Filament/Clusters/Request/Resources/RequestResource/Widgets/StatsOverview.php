<?php

namespace App\Filament\Clusters\Request\Resources\RequestResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->id();

        $leaveAllowance = DB::table('users')
            ->where('id', $user)
            ->value('leave_allowance');

        $sickAllowance = DB::table('users')
            ->where('id', auth()->id())
            ->value('sick_allowance');

        $giveBirthAllowance = DB::table('users')
            ->where('id', auth()->id())
            ->value('give_birth_allowance');

        $totalRequest = DB::table('requests')
            ->where('user_id', $user)
            ->count();

        $inProcess = DB::table('requests')
            ->where('user_id', $user)
            ->whereNotIn('status', ['three', 'four'])
            ->count();

        $rejected = DB::table('requests')
            ->where('user_id', $user)
            ->where('status', 'four')
            ->count();

        return [
            Stat::make('Sisa Cuti Tahunan', $leaveAllowance),
            Stat::make('Sisa Cuti Sakit', $sickAllowance),
            Stat::make('Sisa Cuti Melahirkan', $giveBirthAllowance),
            Stat::make('Jumlah Pengajuan', $totalRequest),
            Stat::make('Dalam Proses', $inProcess),
            Stat::make('Ditolak', $rejected),
        ];
    }
}
