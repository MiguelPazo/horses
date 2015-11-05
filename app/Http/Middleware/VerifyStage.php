<?php namespace Horses\Http\Middleware;

use Closure;
use Horses\CategoryUser;
use Horses\Constants\ConstDb;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

class VerifyStage
{
    protected $_request;
    protected $_guard;

    public function __construct(Request $request, Guard $auth)
    {
        $this->_request = $request;
        $this->_guard = $auth;
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
        $category = $this->_request->session()->get('oCategory');
        $oCatUser = CategoryUser::jury($this->_guard->getUser()->id)->category($category->id)->first();

        $uri = $this->_request->getUri();
        $uris = [route('tournament.selection'), route('tournament.classify_1'), route('tournament.classify_2')];

        if (in_array($uri, $uris)) {
            switch ($oCatUser->actual_stage) {
                case ConstDb::STAGE_SELECTION:
                    if ($uri != route('tournament.classify_1')) {
                        return redirect()->route('tournament.classify_1');
                    }
                    break;
                case ConstDb::STAGE_CLASSIFY_1:
                    if ($uri != route('tournament.classify_2')) {
                        return redirect()->route('tournament.classify_2');
                    }
                    break;
                case ConstDb::STAGE_CLASSIFY_2:
                    return redirect()->to('/auth/logout');
                    break;
            }
        }

        return $next($request);
    }

}
