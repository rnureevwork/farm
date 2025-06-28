<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StationDataRequest extends FormRequest
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
            'serial' => 'required|string',
            'token' => 'required|string',
            'data' => 'required|array',
            'data.temperature' => 'nullable|numeric',
            'data.humidity' => 'nullable|numeric|min:0|max:100',
            'data.soil_moisture' => 'nullable|numeric|min:0|max:100',
            'data.pressure' => 'nullable|numeric',
            'data.wind_speed' => 'nullable|numeric|min:0',
            'data.wind_direction' => 'nullable|numeric|min:0|max:360',
            'data.rainfall' => 'nullable|numeric|min:0',
            'data.battery' => 'nullable|numeric|min:0|max:100',
            'data.signal' => 'nullable|numeric|min:0|max:100',
            'data.gps' => 'nullable|array',
            'data.gps.lat' => 'nullable|numeric',
            'data.gps.lng' => 'nullable|numeric',
            'data.timestamp' => 'nullable|date',
        ];
    }
}
