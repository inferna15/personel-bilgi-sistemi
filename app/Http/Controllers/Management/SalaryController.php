<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalaryRequest;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SalaryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Salary::class);

        $search = $request->input('search');

        $query = Salary::with('user:id,first_name,last_name')
            ->when($search, function ($q) use ($search) {
            $q->whereHas('user', function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        });

        $salaries = $query->paginate(10)->withQueryString();

        return Inertia::render('Management/Salaries/Index', [
            'salaries' => $salaries,
            'current_page' => $salaries->currentPage(),
            'last_page' => $salaries->lastPage(),
            'total' => $salaries->total(),
            'prev_page_url' => $salaries->previousPageUrl(),
            'next_page_url' => $salaries->nextPageUrl(),
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Salary::class);
        return Inertia::render('Management/Salaries/Create', [
            'users' => User::select(['id', 'first_name', 'last_name'])->get(),
        ]);
    }

    public function store(SalaryRequest $request)
    {
        $this->authorize('create', Salary::class);

        $validatedData = $request->validated();

        if ($request->hasFile('payroll_file')) {
            $filePath = $request->file('payroll_file')->store('payrolls', 'public');
            $validatedData['payroll_file_path'] = $filePath;
        }

        Salary::create($validatedData);

        return redirect()->route('management.salaries.index');
    }

    public function edit(Salary $salary)
    {
        $this->authorize('update', $salary);
        $salary->load('user:id,first_name,last_name');

        return Inertia::render('Management/Salaries/Edit', [
            'salary' => $salary,
        ]);
    }

    public function update(SalaryRequest $request, Salary $salary)
    {
        $this->authorize('update', $salary);

        $validatedData = $request->validated();
        $currentFilePath = $salary->payroll_file_path;

        if ($request->hasFile('payroll_file')) {
            if ($currentFilePath) {
                Storage::disk('public')->delete($currentFilePath);
            }
            $filePath = $request->file('payroll_file')->store('payrolls', 'public');
            $validatedData['payroll_file_path'] = $filePath;
        } elseif ($request->input('payroll_file') === null && $currentFilePath) {
            Storage::disk('public')->delete($currentFilePath);
            $validatedData['payroll_file_path'] = null;
        } else {
            $validatedData['payroll_file_path'] = $currentFilePath;
        }

        $salary->update($validatedData);

        return redirect()->route('management.salaries.index');;
    }

    public function destroy(Salary $salary)
    {
        $this->authorize('delete', $salary);

        if ($salary->payroll_file_path) {
            Storage::disk('public')->delete($salary->payroll_file_path);
        }

        $salary->delete();

        return redirect()->route('management.salaries.index');
    }
}
