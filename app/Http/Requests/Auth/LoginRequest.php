<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\ApiAuthService;

class LoginRequest extends FormRequest
{
    protected $apiAuth;
    
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id_customer' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Inisialisasi ApiAuthService
        $this->apiAuth = app(ApiAuthService::class);
        
        // Coba login via API
        $result = $this->apiAuth->attemptLogin(
            $this->id_customer,
            $this->password
        );
        
        // Cek hasil login
        if (!isset($result['success']) || !$result['success']) {
            RateLimiter::hit($this->throttleKey());
            
            $message = $result['message'] ?? 'NIK atau Password salah';
            
            throw ValidationException::withMessages([
                'id_customer' => $message,
            ]);
        }

        // Login sukses - simpan data user ke session
        if (isset($result['data'])) {
            session([
                'api_user' => $result['data'],
                'nik' => $result['data']['nik'] ?? $this->id_customer,
                'username' => $result['data']['nama'] ?? '',
                'level' => $result['data']['level'] ?? '',
                'plant' => $result['data']['plant'] ?? '',
                'kode_jabatan' => $result['data']['kode_jabatan'] ?? '',
                'divisi' => $result['data']['divisi'] ?? '',
                'dept' => $result['data']['dept'] ?? '',
                'jabatan' => $result['data']['jabatan'] ?? '',
                'posisi' => $result['data']['posisi'] ?? []
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'id_customer' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('id_customer')).'|'.$this->ip());
    }
}