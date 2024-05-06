<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\RequestException;
use App\Marketplace\PGP;
use App\User;
use Exception;
use Illuminate\Foundation\Http\FormRequest;

class RecoverPasswordPgpRequest extends FormRequest
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
        return [
            'username' => 'required|exists:users,username',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username is required',
            'username.exists' => 'User with that username does not exist',
        ];
    }

    public function persist()
    {
        $user = User::where('username', $this->username)->first();
        // check if user exist
        if ($user == null) {
            throw new RequestException('Could not find user with that username');
        }

        if ($user->pgp_key == null) {
            throw new RequestException('This user does not have pgp key');
        }

        $validationNumber = mt_rand(100000000000, 999999999999); // Radnom number to confirm
        $decryptedMessage = "You have successfully decrypted this message.\nTo validate this key please copy validation number to the field on the site\nValidation number:" . $validationNumber;

        // Encrypt throws \Exception
        try {
            $encryptedMessage = PGP::EncryptMessage($decryptedMessage, $user->pgp_key);
        } catch (Exception $e) {
            throw new RequestException($e->getMessage());
        }

        // store data to sessions

        session()->put(PGP::NEW_PGP_VALIDATION_NUMBER_KEY, $validationNumber);
        session()->put(PGP::NEW_PGP_ENCRYPTED_MESSAGE, $encryptedMessage);
        session(
            [
                'username' => $user->username,
            ]
        );

        return redirect()->route('auth.pgprecover');
    }
}
