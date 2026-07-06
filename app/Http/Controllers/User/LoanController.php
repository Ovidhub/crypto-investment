<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display the loan application form
     */
        public function index()
        {
            // This shows the History Table
            $loans = DB::table('loans')->where('user', Auth::user()->id)->orderBy('id', 'desc')->get();
            
            return view('user.loan_history', [
                'title' => 'Loan History',
                'loans' => $loans,
                'stats' => [
                    'total' => $loans->count(),
                    'approved' => $loans->where('active', 'Approved')->count(),
                    'pending' => $loans->where('active', 'Pending')->count(),
                    'value' => $loans->sum('amount')
                ]
            ]);
        }
        
        public function create()
        {
            // This shows the Application Form
            return view('user.loan_apply', [
                'title' => 'Apply for Loan'
            ]);
        }

    /**
     * Handle the form submission
     */
    public function store(Request $request)
    {
        // 1. Validate the data to prevent database errors
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'income' => 'required',
            'purpose' => 'required',
            'duration' => 'required',
            'facility' => 'required',
        ]);

        // 2. Save the loan application to the database
        $loanId = DB::table('loans')->insertGetId([
            'user' => Auth::user()->id,
            'amount' => $request->amount,
            'income' => $request->income,
            'purpose' => $request->purpose,
            'duration' => $request->duration,
            'facility' => $request->facility,
            'active' => 'Pending',
            'inv_duration' => $request->duration,
            'activated_at' => Carbon::now(),
            'last_growth' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->back()
            ->with('success', "Application successful! Your loan is currently pending and under review.");
    }
}