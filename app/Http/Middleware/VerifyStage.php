<?php namespace Horses\Http\Middleware;

use Closure;
use Horses\Constants\Db;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

class VerifyStage
{
    protected $request;
    protected $auth;

    public function __construct(Request $request, Guard $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        return new RedirectResponse(route('tournament.result'));
//        $uri = $this->request->getUri();
//
////        dd($uri);
//        $uriExcept = [route('tournament.save.selection'),
//            route('tournament.save.classify_1'),
//            route('tournament.save.classify_2')];
//
//        if (!in_array($uri, $uriExcept)) {
//
//            dd($oCategory);
//        }

        return $next($request);
    }

}
