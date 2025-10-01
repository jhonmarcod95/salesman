<?php

namespace App\Http\Middleware;

use App\HRISUser;
use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class HRISPortalLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $client = new Client([
            'verify' => false,
            'timeout' => env('PORTAL_USER_LOGGER_TIMEOUT'),
        ]);
        try {
            $response = $client->post(env('PORTAL_USER_LOGGER_URL'), [
                'form_params' => [
                    'useragent' => $request->userAgent(),
                    'ipaddress' => $request->ip(),
                    'user_id' => Auth::check() ? (HRISUser::where('email', Auth::user()->email)->firstOrFail())->id: null,
                    'portal_id' => env('PORTAL_USER_LOGGER_PORTAL_ID'),
                    'portal' => Route::currentRouteName(),
                    'url' => $request->fullUrl(),
                    'url_name' => Route::currentRouteName(),
                    // 'url_description' => Route::current()->getAction('description') ?? 'No description provided',
                    // 'url_purpose' => Route::current()->getAction('purpose') ?? 'No purpose provided',
                    'url_description' => 'No description provided',
                    'url_purpose' => 'No purpose provided',
                    'url_method' => $request->method(),
                    'is_authenticated' => Auth::check() ? 1 : 0,
                ]
            ]);

            if ($response->getStatusCode() >= 400) {
                Log::error('Failed to log portal activity:'.$response->getBody()->getContents());
            }
        } catch (RequestException $e) {
            Log::error('Failed to log portal activity:'. $e->getMessage());
        }

        return $next($request);
    }
}