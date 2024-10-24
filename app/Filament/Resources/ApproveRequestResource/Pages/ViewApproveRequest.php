<?php

namespace App\Filament\Resources\ApproveRequestResource\Pages;

use App\Enum\StatusRequest;
use App\Enum\TypeRequest;
use App\Filament\Resources\ApproveRequestResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ViewApproveRequest extends ViewRecord
{
    protected static string $resource = ApproveRequestResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Get nip, name
        $user = User::query()
            ->findOrFail($data['user_id'], ['nip', 'name']);

        $data['nip'] = $user->nip;
        $data['name'] = $user->name;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->buildApproveAction(),
            $this->buildRejectAction()
        ];
    }

    protected function buildApproveAction(): Action
    {
        return Action::make('Approve')
            ->requiresConfirmation()
            ->icon('heroicon-m-hand-thumb-up')
            ->hidden(fn() => $this->isApprovalHidden())
            ->action(fn() => $this->approveAction());
    }

    protected function buildRejectAction(): Action
    {
        return Action::make('Tolak')
            ->requiresConfirmation()
            ->icon('heroicon-m-x-circle')
            ->color('danger')
            ->hidden(fn() => $this->isApprovalHidden())
            ->action(fn() => $this->record->update(['status' => StatusRequest::Four]));
    }

    protected function isApprovalHidden(): bool
    {
        $user = auth()->user();
        $status = $this->record->status;

        // If statement isHeadOfDivision
        if (
            $user->isHeadOfDivision() && // AND
            in_array($status, [
                StatusRequest::One,
                StatusRequest::Two,
                StatusRequest::Four
            ])
        ) {
            return true;
        }

        // If statement isDirector()
        if (
            $user->isDirector() && // AND
            (in_array(
                $status,
                [
                    StatusRequest::Three,
                    StatusRequest::Four
                ]
            ))
        ) {
            return true;
        }

        // If statement isDirector() and check hasRole employee
        if (
            $user->isDirector() && // AND
            ($this->record->status == StatusRequest::Zero && // AND
                ($this->record->user->hasRole('employee')))
        ) {
            return true;
        }

        // If statement isAdmin()
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    protected function approveAction()
    {
        $user = auth()->user();

        $status =  match (true) {
            $user->isHeadOfDivision() => StatusRequest::One,
            $user->isResource() => StatusRequest::Two,
            $user->isDirector() => StatusRequest::Three
        };

        // Update status
        $this->record->update(['status' => $status]);

        // Get differentDays
        $startDate = Carbon::parse($this->record->start_date)->addDay(-1);
        $endDate = Carbon::parse($this->record->end_date);
        $differentDays = $startDate->diffInDays($endDate);

        // Decrement leave allowance logic for headOfDivision, resource
        if (
            $this->record->status == StatusRequest::Three && // AND
            $this->record->type == TypeRequest::Leave
        ) {
            DB::table('users')
                ->where('id', $this->record->user_id)
                ->decrement('leave_allowance', $differentDays);
        }

        // Decrement leave allowance logic for employee
        if (
            $this->record->status == StatusRequest::Two && // AND
            $this->record->type == TypeRequest::Leave && // AND
            $this->record->user->hasRole('employee')
        ) {
            DB::table('users')
                ->where('id', $this->record->user_id)
                ->decrement('leave_allowance', $differentDays);
        }

        // Insert request details/log
        DB::table('request_details')
            ->insert([
                'id' => Str::uuid(),
                'approve_by' => auth()->id(),
                'request_id' => $this->record->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
    }
}
