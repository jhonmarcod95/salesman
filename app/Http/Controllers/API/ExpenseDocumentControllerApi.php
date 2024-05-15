<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Expense;
use App\ExpensesEntry;
use Carbon\Carbon;

class ExpenseDocumentControllerApi extends Controller
{
    /**
     * list expense entries by user
     */
    public function expenseEntries(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
   
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search_name = $request->input('search_name');
    
        return ExpensesEntry::select('id','user_id','totalExpenses','created_at')
                    ->with('user','expensesModel.payments','expensesModel.expensesType')
                    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [Carbon::parse($startDate), Carbon::parse($endDate)]);
                    })
                    ->when($search_name, function ($q) use ($search_name) {
                        $q->whereHas('user', function ($query) use ($search_name) {
                            $query->where('name','LIKE','%'.$search_name.'%');
                        });
                    })
                    ->has('expensesModel')
                    ->withCount('expensesModel')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
    }

    public function expenseDmsReceived(Request $request, Expense $expense)
    {
        $this->validate($request,[
            'dms_reference' => 'required'
        ]);

        $expense->dms_reference = $request->dms_reference;
        $expense->save();

        return $expense;
    }
}
