<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Expense;
use Carbon\Carbon;
use App\ExpensesEntry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ExpenseDocumentControllerApi extends Controller
{
    /**
     * list expense entries by user
     */
    public function expenseEntries2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
   
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $user_id = $request->input('user_id');

        $expenses_entry = ExpensesEntry::select('id','user_id','totalExpenses','created_at')
                    ->with('user:id,name', 'verifiedExpense', 'verifiedExpense.expensesType:id,name')
                    ->whereBetween('created_at', [Carbon::parse($startDate), Carbon::parse($endDate)])
                    ->whereHas('user', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    })
                    ->with(['verifiedExpense' => function ($q) {
                        $q->whereNull('dms_reference');
                    }])
                    ->has('verifiedExpense')
                    ->withCount('verifiedExpense')
                    ->get();

        $verified_expense_count = 0;
        $total_expenses = 0;
        $expense_attachments = [];
        foreach($expenses_entry as $expense) {
            $verified_expense_count = $verified_expense_count + $expense->verified_expense_count;
            $total_expenses = $total_expenses + $expense->totalExpenses;

            foreach($expense->verifiedExpense as $verified) {
                $data = [];
                $data['id'] = $verified->id;
                $data['expenses_entry_id'] = $verified->expenses_entry_id;
                $data['attachment'] = $verified->attachment;
                $data['expenses_type'] = $verified->expensesType->name;
                $expense_attachments[] = $data;
            }
        }

        $expense_data = [
            'user' => User::find($user_id, ['id', 'name']),
            'expense_attachments' => $expense_attachments,
            'expense_attachment_count' => count($expense_attachments),
            'total_expenses' => $total_expenses,
        ];

        return $expense_data;
    }

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
        $limit = $request->input('limit');

        return ExpensesEntry::select('id', 'user_id', 'totalExpenses', 'created_at')
        ->with('user', 'expensesModel.payments', 'expensesModel.expensesType', 'verifiedExpense')
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate), Carbon::parse($endDate)]);
        })
            ->when($search_name, function ($q) use ($search_name) {
                $q->whereHas('user', function ($query) use ($search_name) {
                    $query->where('name', 'LIKE', '%' . $search_name . '%');
                });
            })
            ->has('verifiedExpense')
            // ->withCount('expensesModel')
            ->orderBy('id', 'desc')
            ->paginate($limit ? $limit : 10);
    }

    public function expenseDmsReceived(Request $request)
    {
        $this->validate($request,[
            'dms_reference' => 'required',
            'expense_ids' => 'required'
        ]);

        foreach($request->expense_ids as $expense_id) {
            $expense = Expense::find($expense_id);
            $expense->dms_reference = $request->dms_reference;
            $expense->save();
        }

        return response()->json(['message' => "Expense Received Success"], 200);
    }    

    public function getTsrUsers() {
        return User::select('id','name')
            ->whereHas('roles', function($q) {
                $q->whereIn('slug', ['tsr','coordinator']);
            })
            ->get();
    }
}
