<?php
namespace App\Http\Controllers;

use App\Models\Entries;
use App\Models\LoanHistory;
use App\Models\LoanRule;
use App\Models\User;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'title'    => 'Loan Rules',
            'template' => 'admin.loan.list',
        ];

        $loanRules = LoanRule::get();

        return view('with_login_common', compact('data', 'loanRules'));
    }

    public function create()
    {
        $retdata          = [];
        $data['title']    = 'Add Loan Rule';
        $data['template'] = 'admin.loan.create';
        return view('with_login_common', compact('data', 'retdata'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'duration'      => 'required|integer|min:1',
            'duration_type' => 'required|string|in:day,week,month',
        ]);

        $durationInDays = match ($request->input('duration_type')) {
            'day' => $request->input('duration'),
            'week' => $request->input('duration') * 7,
            'month' => $request->input('duration') * 30,
            default => $request->input('duration'),
        };

        $existingRule = LoanRule::where('duration', $durationInDays)->first();

        if ($existingRule) {
            return back()->with('error', 'A loan rule with the same duration already exists.');
        }

        LoanRule::create([
            'title'    => $request->input('title'),
            'duration' => $durationInDays,
        ]);

        return back()->with('success', 'Loan Rule added successfully!');
    }

    public function edit($id)
    {
        $loanRule         = LoanRule::findOrFail($id);
        $data['title']    = 'Edit Loan Rule';
        $data['template'] = 'admin.loan.edit';
        return view('with_login_common', compact('data', 'loanRule'));
    }

    public function destroy($id)
    {
        try {
            $rule = LoanRule::findOrFail($id);

            $rule->delete();

            return response()->json(['success' => true, 'message' => 'Loan rule deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete the loan rule.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'duration'      => 'required|integer|min:1',
            'duration_type' => 'required|string|in:day,week,month',
        ]);

        $durationInDays = match ($request->input('duration_type')) {
            'day' => $request->input('duration'),
            'week' => $request->input('duration') * 7,
            'month' => $request->input('duration') * 30,
            default => $request->input('duration'),
        };

        $existingRule = LoanRule::where('duration', $durationInDays)
            ->where('id', '!=', $id)
            ->first();

        if ($existingRule) {
            return back()->with('error', 'A loan rule with the same duration already exists.');
        }

        $loanRule = LoanRule::findOrFail($id);

        $loanRule->update([
            'title'    => $request->input('title'),
            'duration' => $durationInDays,
        ]);

        return redirect()->route('loan-rules.index')->with('success', 'Loan Rule updated successfully!');
    }

    public function overdueRentItems(Request $request)
    {
        $data = [
            'title'    => 'Over Due Rent Items',
            'template' => 'admin.loan.overdue-rent-items',
        ];

        $overdueItems = Entries::where('is_reserved', 1)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())->with(['book', 'book.group', 'reserver'])->get();

        return view('with_login_common', compact('data', 'overdueItems'));
    }

    public function loanHistory(Request $request)
    {
        $data = [
            'title'    => 'Loan History',
            'template' => 'admin.loan.loan-history',
        ];

        $loanHistory = LoanHistory::with(['book', 'user', 'group'])->get();

        return view('with_login_common', compact('data', 'loanHistory'));
    }

    public function reservationReport(Request $request)
    {
        $data = [
            'title'    => 'Reservation Report',
            'template' => 'admin.loan.reservation-report',
        ];

        $request->validate([
            'item'        => 'nullable|string|max:60',
            'reserved_by' => 'nullable|exists:users,id',
            'from_date'   => 'nullable|date',
            'to_date'     => 'nullable|date|after_or_equal:from_date',
        ]);

        $members = User::whereIn('id', Entries::distinct()->pluck('reserved_by'))->get(['id', 'name']);

        $reservationReport = Entries::with(['book.group', 'reserver'])
            ->whereHas('book', function ($query) use ($request) {
                if ($request->filled('item')) {
                    $query->where('name', 'like', '%' . $request->item . '%')
                        ->orWhere('id', $request->item);
                }
            })
            ->whereHas('book.group')
            ->where(function ($query) {
                $query->where('is_reserved', 1)
                    ->orWhere('state', 'return-request');
            })
            ->when($request->filled('reserved_by'), function ($query) use ($request) {
                $query->where('reserved_by', $request->reserved_by);
            })
            ->when($request->filled('from_date'), function ($query) use ($request) {
                $query->whereDate('reserved_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($query) use ($request) {
                $query->whereDate('due_date', '<=', $request->to_date);
            })
            ->get();

        return view('with_login_common', compact('data', 'reservationReport', 'members'));
    }

}
