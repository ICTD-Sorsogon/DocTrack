<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !$this->document || auth()->user()->can('update', $this->document);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject' => 'required|string',
            'is_external' => 'required|boolean',
            'document_type_id' => 'required|digits_between: 1, 999',
            'destination_office_id' => 'required|array',
            'destination_office_id.*' => 'required|digits_between:1,999',
            'sender_name' => 'required',
            'page_count' => 'required|digits_between:1, 99999',
            'remarks' => 'required|string',
            'attachment_page_count' => 'required|digits_between:1,99999',
        ];
    }
}
