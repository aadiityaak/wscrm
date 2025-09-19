<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'provider' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'next_billing' => ['nullable', 'date'],
            'paid_date' => ['nullable', 'date', 'before_or_equal:today'],
            'status' => ['required', 'in:active,inactive,pending,paid,cancelled'],
            'type' => ['required', 'in:monthly,yearly,one-time'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama pengeluaran wajib diisi.',
            'amount.required' => 'Jumlah pengeluaran wajib diisi.',
            'amount.numeric' => 'Jumlah pengeluaran harus berupa angka.',
            'amount.min' => 'Jumlah pengeluaran tidak boleh kurang dari 0.',
            'currency.required' => 'Mata uang wajib diisi.',
            'currency.size' => 'Mata uang harus 3 karakter.',
            'provider.required' => 'Penyedia layanan wajib diisi.',
            'paid_date.before_or_equal' => 'Tanggal pembayaran tidak boleh di masa depan.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status tidak valid.',
            'type.required' => 'Tipe pengeluaran wajib diisi.',
            'type.in' => 'Tipe pengeluaran tidak valid.',
        ];
    }
}
