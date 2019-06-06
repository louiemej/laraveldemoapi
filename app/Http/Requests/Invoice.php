<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Invoice extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function validator($factory)
    {
    return $factory->make(
        $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
    );
    }

    public function sanitize()
    {
        $data = json_decode($this->getContent(), true);
        $this->merge([
            'invoice' => $data['invoice'],
            'lines' => $data['lines']
        ]);
        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];
        $rules['invoice.sold_to'] = 'required';
        $rules['invoice.business_style'] = 'required';
        $rules['invoice.created_at'] = 'required';
        $rules['invoice.address'] = 'required';

        for ($i = 0; $i < count($this->lines); $i++) {
            $rules['lines.' . $i . '.description'] = 'required';
            $rules['lines.' . $i . '.quantity'] = 'required|integer';
            $rules['lines.' . $i . '.price'] = 'required';
        }

        return $rules;
    }
}
