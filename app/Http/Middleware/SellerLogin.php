<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use DB;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;


class SellerLogin
{
    /**
     * @param string $tokenId
     *
     * @return mixed
     */
    private function isAccessTokenRevoked($tokenId)
    {
        return DB::table('oauth_access_tokens')
            ->where('id', $tokenId)
            ->where('revoked', 1)
            ->exists();
    }

    /**
     * @param string $jwt
     *
     * @return array|bool
     */
    private function validateToken($jwt)
    {
        try {
            $token = (new Parser())->parse($jwt);

            if ($token->verify(new Sha256(), file_get_contents(Passport::keyPath('oauth-public.key'))) === false) {
                return false;
            }

// Ensure access token hasn't expired.
            $data = new ValidationData();
            $data->setCurrentTime(time());

            if ($token->validate($data) === false) {
                return false;
            }

// Check if token has been revoked.
            if ($this->isAccessTokenRevoked($token->getClaim('jti'))) {
                return false;
            }

            return [
                'user_id' => $token->getClaim('sub'),
            ];
        } catch (\Exception $e) {
            return false; // Decoder error.
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

// If user passed a valid Passport token, then login to the webview.
        if (!empty($token) && $request->hasSession() && !Auth::check() && $user_id = $this->validateToken($token)) {
            \Auth::loginUsingId($user_id);
        }
        return $next($request);
    }
}
