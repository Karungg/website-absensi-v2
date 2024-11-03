<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PdfController extends Controller
{
    public function index(string $id)
    {
        $request = ModelsRequest::query()
            ->with([
                'user:id,nip,name,date_of_entry,phone,leave_allowance,sick_allowance,give_birth_allowance,position_id',
                'user.position:id,title'
            ])
            ->where('id', $id)
            ->firstOrFail([
                'id',
                'type',
                'description',
                'start_date',
                'end_date',
                'location',
                'user_id',
                'updated_at'
            ]);

        // Get leave amount
        $startDate = Carbon::parse($request->start_date)->addDays(-1);
        $endDate = Carbon::parse($request->end_date);
        $leaveAmount = $startDate->diffInDays($endDate);

        // Get oldest request details
        $oldest = $this->getRequestDetails($request->id, 'oldest');

        // Get latest request details
        $latest = $this->getRequestDetails($request->id, 'latest');

        // Get headOfDivision name from oldest request details
        $headOfDivisionName = $this->getApproveBy($oldest->approve_by);

        // Get resourceOrDirector from latest request details
        $resourceOrDirector = $this->getApproveBy($latest->approve_by);

        $pdf = Pdf::loadView('pdf', [
            'request' => $request,
            'leaveAmount' => $leaveAmount,
            'headOfDivisionName' => $headOfDivisionName,
            'resourceOrDirector' => $resourceOrDirector
        ]);

        return $pdf->stream('formulir-cuti-tahunan-' . $request->user->name . '.pdf');
    }

    private function getRequestDetails(string $requestId, string $order)
    {
        return DB::table('request_details')
            ->where('request_id', $requestId)
            ->{$order}()
            ->first(['approve_by']);
    }

    private function getApproveBy(string $id)
    {
        return DB::table('users')
            ->where('id', $id)
            ->value('name');
    }
}
