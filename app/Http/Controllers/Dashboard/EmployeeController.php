<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Employee\StoreEmployeeRequest;
use App\Models\User;
use App\Notifications\EmployeeCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        Gate::authorize(ability: 'viewAny', arguments: User::class);

        $employees = auth()->user()->children()->paginate();

        return view(view: 'dashboard.pages.employees.index', data: [
            'employees' => $employees,
        ]);
    }

    public function create(): View
    {
        Gate::authorize(ability: 'create', arguments: User::class);

        return view(view: 'dashboard.pages.employees.create');
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $password = Str::password();

        $employee = DB::transaction(callback: function () use ($request, $password): User {
            $employee = $request->user()->children()->create(attributes: [
                'name' => $request->validated(key: 'name'),
                'email' => $request->validated(key: 'email'),
                'password' => $password,
            ]);

            $employee->assignRole(UserRole::EMPLOYEE->asModel());

            return $employee;
        });

        $employee->notify(instance: new EmployeeCreated(email: $employee->email, password: $password));

        $message = __(key: 'dashboard.messages.model_has_been_successfully_created', replace: ['model' => __(key: 'models.employee')]);

        return to_route(route: 'dashboard.employees.index')->with(key: 'success', value: $message);
    }

    public function destroy(User $employee): RedirectResponse
    {
        Gate::authorize(ability: 'delete', arguments: $employee);

        $employee->delete();

        $message = __(key: 'dashboard.messages.model_has_been_successfully_deleted', replace: ['model' => __(key: 'models.employee')]);

        return to_route(route: 'dashboard.employees.index')->with(key: 'success', value: $message);
    }
}
