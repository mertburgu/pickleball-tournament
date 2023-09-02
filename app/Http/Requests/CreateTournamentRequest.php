<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTournamentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'game_format' => 'required',
            'score_format' => 'required',
            'tournament_format' => 'required',
            'average_game_time' => 'required',
            'number_of_courts' => 'required',
            'player_limit' => 'required|integer|player_limit_multiple:' . $this->input('game_format'),
        ];
    }
}
