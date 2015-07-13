<?php namespace Horses\Http\Controllers\Auth;

use Horses\Category;
use Horses\Jury;
use Horses\Tournament;
use Horses\Http\Controllers\Controller;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    public function __construct(Guard $auth, Registrar $registrar)
    {
        $this->auth = $auth;

        $this->middleware('guest', ['except' => 'getLogout']);
    }

    public function getLogin()
    {
        $tournament = Tournament::where('activo', '=', 1)->firstOrFail();
        $lstCategory = Category::where('concurso_id', '=', $tournament->id)->get();

        return view('auth.login')
            ->with('tournament', $tournament)
            ->with('lstCategory', $lstCategory);
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'user' => 'required',
            'password' => 'required',
            'category' => 'required'
        ]);

        $response = [
            'success' => true,
            'message' => '',
            'url' => ''
        ];

        $user = $request->get('user');
        $pass = $request->get('password');
        $category = $request->get('category');

        $oUser = Jury::where('usuario', '=', $user)->firstOrFail();

        if ($oUser->password == $pass) {
            $authUser = new GenericUser($oUser->toArray());
            $this->auth->login($authUser);

            $oCategory = Category::find($category);

            $request->session()->put('category', $oCategory);
            $response['url'] = $this->redirectPath();
        } else {
            $response['message'] = 'Ha ocurrido un error, vuelva a intentarlo';
            $response['success'] = false;
        }


        return response()->json($response);
    }

    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : route('tournament.selection');
    }

    public function loginPath()
    {
        return property_exists($this, 'loginPath') ? $this->loginPath : '/';
    }

    public function getLogout()
    {
        $this->auth->logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

}
