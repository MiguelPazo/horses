<?php namespace Horses\Http\Controllers\Auth;

use Horses\Category;
use Horses\CategoryJury;
use Horses\Constants\ConstDb;
use Horses\User;
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
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'user' => 'required',
            'password' => 'required'
        ]);

        $response = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $user = $request->get('user');
        $pass = $request->get('password');
        $oUser = User::user($user)->first();

        if ($oUser) {
            if ($oUser->password == $pass) {
                if ($oUser->login != ConstDb::USER_CONECTED) {

                    if ($oUser->type == ConstDb::USER_TYPE_OPERATOR || $oUser->type == ConstDb::USER_TYPE_JURY) {
                        $oUser->login = ConstDb::USER_CONECTED;
                        $oUser->save();

                        $authUser = new GenericUser($oUser->toArray());
                        $this->auth->login($authUser);

                        $response['success'] = true;
                    }

                    switch ($oUser->type) {
                        case ConstDb::USER_TYPE_OPERATOR:

//                            $request->session()->put('category', $oCategory);
//                            $request->session()->put('dirimente', $jDirimente);
                            $response['url'] = route('admin.tournament.index');
                            break;
                        case ConstDb::USER_TYPE_JURY:
//                            $response['url'] = $this->redirectPath();
                            $response['url'] = route('tournament.selection');
                            break;
                    }
                } else {
                    $response['message'] = 'El usuario ya se encuentra conectado, comuniquese con el administrador del sistema.';
                }
            } else {
                $response['message'] = 'La contraseña es incorrecta, vuelva a intentarlo.';
            }
        } else {
            $response['message'] = 'El usuario ingresado no existe.';
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
        $oUser = User::find($this->auth->user()->id);
        $oUser->estado = ConstDb::USER_ACTIVE;
        $oUser->save();

        $this->auth->logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

}
