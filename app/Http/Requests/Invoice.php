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
            // $rules['lines.' . $i . '.price'] = 'required|integer';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        // return [
        //     'title.required' => 'A title is required',
        //     'body.required'  => 'A message is required',
        // ];

        $messages = [];
        $messages['invoice.sold_to.required'] = 'This field is required';
        $messages['invoice.business_style.required'] = 'This field is required';
        $messages['invoice.created_at.required'] = 'This field is required';
        $messages['invoice.address.required'] = 'This field is required';
        for ($i = 0; $i < count($this->lines); $i++) {
            $messages['lines.' . $i . '.description.required'] = 'This field is required';
            $messages['lines.' . $i . '.quantity.required'] = 'This field is required';
            $messages['lines.' . $i . '.price.required'] = 'This field is required';
            $messages['lines.' . $i . '.quantity.integer'] = 'This field must be an integer';
            $messages['lines.' . $i . '.price.integer'] = 'This field must be an integer';
        }

        return $messages;
    }
}
