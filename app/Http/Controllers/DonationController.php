<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    
    public function index()
    {
        $donations = Donation::with(['admin', 'transfers.manager'])->get();

        
        $total = Donation::sum('amount');

        return response()->json([
            'donations' => $donations,
            'total_amount' => $total
        ]);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'donor' => 'required|string'
        ]);

        $donation = Donation::create([
            'amount' => $request->amount,
            'donor' => $request->donor,
            'admin_id' => auth()->id()
        ]);

        return response()->json($donation, 201);
    }

    
    public function getManagers()
    {
        
        $managers = DB::table('managers')->select('id', 'name')->get();

        return response()->json([
            'success' => true,
            'managers' => $managers,
            'count' => $managers->count()
        ]);
    }


    public function transfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'manager_id' => 'required|exists:managers,id'
        ]);

        
        $currentBalance = Donation::sum('amount');

        if ($request->amount > $currentBalance) {
            return response()->json([
                'message' => 'المبلغ المطلوب أكبر من الرصيد المتاح'
            ], 400);
        }

        
        $transfer = Transfer::create([
            'amount' => $request->amount,
            'manager_id' => $request->manager_id,
            'donation_id' => null
        ]);

        
        Donation::create([
            'amount' => -$request->amount, 
            'donor' => 'سحب للمدير - تحويل',
            'admin_id' => auth()->id()
        ]);

        
        $newBalance = $currentBalance - $request->amount;

        return response()->json([
            'message' => 'تم التحويل بنجاح من إجمالي التبرعات',
            'transfer' => $transfer,
            'previous_balance' => $currentBalance,
            'transfer_amount' => $request->amount,
            'new_balance' => $newBalance
        ], 201);
    }

    
    public function donationStats()
    {
        $totalDonations = Donation::where('amount', '>', 0)->sum('amount');
        $totalTransfers = abs(Donation::where('amount', '<', 0)->sum('amount'));
        $currentBalance = Donation::sum('amount');
        
        return response()->json([
            'total_donations' => $totalDonations,
            'total_transfers' => $totalTransfers,
            'current_balance' => $currentBalance
        ]);
    }


    public function managerTransfers()
    {
        $managerId = auth()->id();
        $transfers = Transfer::with('donation')->where('manager_id', $managerId)->get();
        return response()->json($transfers);
    }


    public function managerStats()
    {
        $managerId = auth()->id();
        $total = Transfer::where('manager_id', $managerId)->sum('amount');
        
        return response()->json([
            'manager_id' => $managerId,
            'total_received' => $total,
            'transfers_count' => Transfer::where('manager_id', $managerId)->count()
        ]);
    }

}