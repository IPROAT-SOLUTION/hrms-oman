<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SocialSecurityRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(isset($this->social_security_id)){
            return [
                'gross_salary'  => 'required',
                'year' => [
                    'required',
                    Rule::unique('social_security')->where(function ($query) {
                        return $query->where('year', $this->year)
                            ->where('nationality', $this->nationality);
                    })->ignore($this->social_security_id , 'social_security_id'),
                ],
                'nationality' => 'required',
                'percentage' => 'required',
                'employer_contribution' => 'required',
            ];
        }

        return [
            'gross_salary'  => 'required',
            // 'year'  => 'required',
            'year' => [
                'required',
                Rule::unique('social_security')->where(function ($query) {
                    return $query->where('year', $this->year)
                        ->where('nationality', $this->nationality);
                }),
            ],
            'nationality' => 'required',
            'percentage' => 'required',
            'employer_contribution' => 'required',
        ];
    }
}
