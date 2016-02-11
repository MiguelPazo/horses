<?php
/**
 * Created by PhpStorm.
 * User: MPazo
 * Date: 11/09/2015
 * Time: 04:18 PM
 */

namespace Horses\Http\Middleware;

use Closure;
use Horses\Constants\ConstDb;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roles = $this->getRequiredRoleForRoute($request->route());
        $user = $request->user();

        if ($user->checkRol($roles) || !$roles) {
            return $next($request);
        } else {
            switch ($user->profile) {
                case ConstDb::PROFILE_ADMIN:
                    return redirect()->route('admin.dashboard');
                    break;
                case ConstDb::PROFILE_JURY:
                    return redirect()->route('tournament.selection');
                    break;
                case ConstDb::PROFILE_COMMISSAR:
                    return redirect()->to('/commissar');
                    break;
                case ConstDb::PROFILE_OPERATOR:
                    return redirect()->route('oper.catalog.index');
                    break;
            }
        }
    }

    private function getRequiredRoleForRoute($route)
    {
        $actions = $route->getAction();
        return isset($actions['roles']) ? $actions['roles'] : null;
    }
}