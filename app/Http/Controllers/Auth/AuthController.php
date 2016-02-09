<?php namespace Horses\Http\Controllers\Auth;

use Horses\Category;
use Horses\CategoryUser;
use Horses\Constants\ConstDb;
use Horses\User;
use Horses\Tournament;
use Horses\Http\Controllers\Controller;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    public function __construct(Guard $auth)
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
        $oUser = User::user($user)->status(ConstDb::STATUS_ACTIVE)->first();

        if ($oUser) {
            if (Hash::check($pass, $oUser->password)) {
                if ($oUser->login != ConstDb::USER_CONECTED || true) {
                    $process = true;

                    switch ($oUser->profile) {
                        case ConstDb::PROFILE_ADMIN:
                            $response['url'] = route('admin.dashboard');
                            break;

                        case ConstDb::PROFILE_COMMISSAR:
                            $process = false;
                            $categoryActive = $this->isCategoryActive();
                            $oCategory = $categoryActive['category'];
                            $oTournament = $categoryActive['tournament'];

                            if ($oCategory) {
                                if ($oCategory->actual_stage == null) {
                                    $process = true;
                                    $request->session()->put('oCategory', $oCategory);
                                    $request->session()->put('oTournament', $oTournament);
                                    $response['url'] = route('commissar.assistance');
                                } else {
                                    $response['message'] = 'No existen categorías pendientes para tomar asistencía.';
                                }

                            } else {
                                $response['message'] = $categoryActive['message'];
                            }

                            break;

                        case ConstDb::PROFILE_JURY:
                            $process = false;
                            $categoryActive = $this->isCategoryActive();
                            $oCategory = $categoryActive['category'];
                            $oTournament = $categoryActive['tournament'];

                            if ($oCategory) {
                                $oCategoryJury = CategoryUser::jury($oUser->id)->category($oCategory->id)->first();

                                if ($oCategoryJury) {
                                    switch ($oCategoryJury->actual_stage) {
                                        case null:
                                            $response['message'] = 'Aún no se ha tomado asistencia, espere un momento por favor.';
                                            break;
                                        case ConstDb::STAGE_ASSISTANCE:
                                            $process = true;
                                            $response['url'] = route('tournament.selection');
                                            break;
                                        case ConstDb::STAGE_SELECTION:
                                            $process = true;
                                            $response['url'] = route('tournament.classify_1');
                                            break;
                                        case ConstDb::STAGE_CLASSIFY_1:
                                            $process = true;
                                            $response['url'] = route('tournament.classify_2');
                                            break;
                                        case ConstDb::STAGE_CLASSIFY_2:
                                            $response['message'] = 'Ya terminó la evaluación, ahora puede ver los resultados públicos.';
                                            break;
                                    }

                                    if ($process) {
                                        $diriment = ($oCategoryJury->dirimente == ConstDb::JURY_DIRIMENT) ? true : false;

                                        $request->session()->put('diriment', $diriment);
                                        $request->session()->put('oCategory', $oCategory);
                                        $request->session()->put('oTournament', $oTournament);
                                    }

                                } else {
                                    $response['message'] = 'Usted no ha sido asignado como jurado a la categoría activa.';
                                }
                            } else {
                                $response['message'] = $categoryActive['message'];
                            }
                            break;
                        case ConstDb::PROFILE_OPERATOR:
                            $oTournament = Tournament::status(ConstDb::STATUS_ACTIVE)->first();

                            if ($oTournament) {
                                $request->session()->put('oTournament', $oTournament);
                                $response['url'] = route('oper.animal.index');
                            } else {
                                $process = false;
                                $response['message'] = 'Aún no se ha activado ningún torneo.';
                            }

                            break;
                    }

                    if ($process) {
                        if ($oUser->profile != ConstDb::PROFILE_ADMIN) {
                            $oUser->login = ConstDb::USER_CONECTED;
                            $oUser->save();
                        }

                        $authUser = new GenericUser($oUser->toArray());
                        $this->auth->login($authUser);

                        $response['success'] = true;
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

    public function isCategoryActive()
    {
        $category = ['category' => null, 'tournament' => null, 'message' => ''];

        $oTournament = Tournament::status(ConstDb::STATUS_ACTIVE)->first();

        if ($oTournament) {
            $oCategory = Category::tournament($oTournament->id)->statusIn([ConstDb::STATUS_ACTIVE, ConstDb::STATUS_IN_PROGRESS])->first();

            if ($oCategory) {
                $category['category'] = $oCategory;
                $category['tournament'] = $oTournament;
            } else {
                $category['message'] = 'Aún no se ha activado ninguna categoría, espere un momento por favor.';
            }
        } else {
            $category['message'] = 'Aún no se ha activado ningún concurso, espere un momento por favor.';
        }

        return $category;
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
        if ($this->auth->user()) {
            $oUser = User::find($this->auth->user()->id);

            if ($oUser) {
                if ($oUser->profile != ConstDb::PROFILE_ADMIN) {
                    $oUser->login = ConstDb::USER_DISCONNECTED;
                    $oUser->save();
                }
            }
        }

        $this->auth->logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

}
