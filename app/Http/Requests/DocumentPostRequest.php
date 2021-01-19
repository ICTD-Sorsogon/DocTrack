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
        return auth()->user()->canEditThisDoc($this->id);
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
            'destination_office_id' => 'required|digits_between:1,999',
            'sender_name' => 'required|string',
            'page_count' => 'required|digits_between:1, 99999',
            'is_terminal' => 'required|boolean',
            'remarks' => 'required|string',
            'attachment_page_count' => 'required|digits_between:1,99999',
        ];
    }
}
