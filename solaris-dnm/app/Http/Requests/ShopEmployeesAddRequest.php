<?php

namespace App\Http\Requests;

use App\Packages\Rules\NotInIcase;
use App\User;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShopEmployeesAddRequest extends FormRequest
{
    protected $redirect = '/shop/management/employees/add';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $shop = Auth::user()->shop();

        return $shop->enabled;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $shop = Auth::user()->shop();

        return [
            'username' => [
                'required',
                Rule::exists('users')->where(function ($query): void {
                    $query->where('role', User::ROLE_USER);
                }),
                new NotInIcase(
                    $shop->employees()->with(['user'])->get()->map(fn ($employee) => $employee->user->username)->toArray()
                ),
            ],
        ];
    }
}
