<?php

namespace App\Http\Requests\Dashboard\Employee;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can(
            abilities: 'createEmployee',
            arguments: User::class,
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(
                    table: 'users',
                    column: 'email',
                ),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __(
                key: 'dashboard.pages.employees.columns.name',
            ),
            'email' => __(
                key: 'dashboard.pages.employees.columns.email',
            ),
        ];
    }
}
