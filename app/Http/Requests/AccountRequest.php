<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class AccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'business_name' => 'required|min:2|max:255',
            'owners' => function ($attribute, $value, $fail) {
                $fieldGroups = json_decode($value);

                // do not allow repeatable field to be empty
                if (count($fieldGroups) == 0) {
                    return $fail('The owners field group must have at least one item.');
                }

                // ALTERNATIVE:
                // allow repeatable field to be empty
                // if (count($fieldGroups) == 0) {
                //   return true;
                // }

                // SECOND-LEVEL REPEATABLE VALIDATION
                // run through each field group inside the repeatable field
                // and run a custom validation for it
                foreach ($fieldGroups as $key => $group) {
                    $fieldGroupValidator = Validator::make((array) $group, [
                        'owner_name' => 'required|min:1|max:255',
                        'title' => 'required|min:1|max:255',
                        'date_of_birth' => 'required|date',
                    ]);

                    if ($fieldGroupValidator->fails()) {
                        // return $fail('One of the entries in the '.$attribute.' group is invalid.');
                        // alternatively, you could just output the first error
                        return $fail($fieldGroupValidator->errors()->first());
                        // or you could use this to debug the errors
                            // dd($fieldGroupValidator->errors());
                    }
                }
            },
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
