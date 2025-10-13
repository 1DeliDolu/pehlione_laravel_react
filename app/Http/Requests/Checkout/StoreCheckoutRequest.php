<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentMethodIds = collect(config('checkout.payment_methods', []))->pluck('id')->all();

        return [
            'shipping_address.id' => ['nullable', 'integer', 'exists:addresses,id'],
            'shipping_address.label' => ['nullable', 'string', 'max:100'],
            'shipping_address.first_name' => ['required_without:shipping_address.id', 'string', 'max:120'],
            'shipping_address.last_name' => ['required_without:shipping_address.id', 'string', 'max:120'],
            'shipping_address.company' => ['nullable', 'string', 'max:150'],
            'shipping_address.line1' => ['required_without:shipping_address.id', 'string', 'max:255'],
            'shipping_address.line2' => ['nullable', 'string', 'max:255'],
            'shipping_address.postal_code' => ['required_without:shipping_address.id', 'string', 'max:32'],
            'shipping_address.city' => ['required_without:shipping_address.id', 'string', 'max:120'],
            'shipping_address.state' => ['nullable', 'string', 'max:120'],
            'shipping_address.country' => ['required_without:shipping_address.id', 'string', 'size:2'],
            'shipping_address.phone' => ['nullable', 'string', 'max:40'],
            'shipping_address.set_as_default_shipping' => ['sometimes', 'boolean'],
            'shipping_address.set_as_default_billing' => ['sometimes', 'boolean'],

            'billing_address.same_as_shipping' => ['sometimes', 'boolean'],
            'billing_address.id' => ['nullable', 'integer', 'exists:addresses,id'],
            'billing_address.label' => ['nullable', 'string', 'max:100'],
            'billing_address.first_name' => ['required_unless:billing_address.same_as_shipping,true', 'required_without_all:billing_address.id,billing_address.same_as_shipping', 'string', 'max:120'],
            'billing_address.last_name' => ['required_unless:billing_address.same_as_shipping,true', 'required_without_all:billing_address.id,billing_address.same_as_shipping', 'string', 'max:120'],
            'billing_address.company' => ['nullable', 'string', 'max:150'],
            'billing_address.line1' => ['required_unless:billing_address.same_as_shipping,true', 'required_without_all:billing_address.id,billing_address.same_as_shipping', 'string', 'max:255'],
            'billing_address.line2' => ['nullable', 'string', 'max:255'],
            'billing_address.postal_code' => ['required_unless:billing_address.same_as_shipping,true', 'required_without_all:billing_address.id,billing_address.same_as_shipping', 'string', 'max:32'],
            'billing_address.city' => ['required_unless:billing_address.same_as_shipping,true', 'required_without_all:billing_address.id,billing_address.same_as_shipping', 'string', 'max:120'],
            'billing_address.state' => ['nullable', 'string', 'max:120'],
            'billing_address.country' => ['required_unless:billing_address.same_as_shipping,true', 'required_without_all:billing_address.id,billing_address.same_as_shipping', 'string', 'size:2'],
            'billing_address.phone' => ['nullable', 'string', 'max:40'],
            'billing_address.set_as_default_billing' => ['sometimes', 'boolean'],

            'payment_method' => ['required', 'string', Rule::in($paymentMethodIds)],
            'discount_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->mergeRecursiveBoolean('shipping_address', [
            'set_as_default_shipping',
            'set_as_default_billing',
        ]);

        $this->mergeRecursiveBoolean('billing_address', [
            'same_as_shipping',
            'set_as_default_billing',
        ]);
    }

    protected function mergeRecursiveBoolean(string $key, array $flags): void
    {
        $data = $this->input($key);
        if (! is_array($data)) {
            return;
        }

        foreach ($flags as $flag) {
            if (array_key_exists($flag, $data)) {
                $data[$flag] = filter_var($data[$flag], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
            }
        }

        $this->merge([$key => $data]);
    }
}

