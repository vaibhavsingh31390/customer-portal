<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\SqlHelper;
use Closure;

class CheckUserToken
{
    public function handle($request, Closure $next)
    {
        if (! $request->session()->has('user_token')) {
            return redirect()->route('login.form')->withErrors([
                'message' => 'You are not logged in.',
            ]);
        }

        $token = $request->session()->get('user_token');
        $userId = decrypt($token);
        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login.form')->withErrors([
                'message' => 'Invalid user token.',
            ]);
        }

        if ($user->user_code && preg_match('/^[S]/', $user->user_code)) {
            $nameRow = SqlHelper::selectOne(
                'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_ENGINEERS).' WHERE working_status = ? AND department = ? AND engineer_code = ? ORDER BY 1',
                ['WK', 'SWE', $user->user_code]
            );
            $name = $nameRow->name ?? null;
        } else {
            $nameRow = SqlHelper::selectOne(
                'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ? ORDER BY 1",
                [$user->user_code]
            );
            $name = $nameRow->name ?? null;
        }

        if (! $request->session()->has('user')) {
            $request->session()->put('user', $user);
        }

        if (! $request->session()->has('name')) {
            $request->session()->put('name', $name);
        }

        return $next($request);
    }
}
