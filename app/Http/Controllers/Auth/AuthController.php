<?php namespace Horses\Http\Controllers\Auth;

use Horses\Category;
use Horses\CategoryJury;
use Horses\Constants\Db;
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
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $user = $request->get('user');
        $pass = $request->get('password');
        $category = $request->get('category');
        $jDirimente = ($request->get('rad_jury_type', Db::JURY_TYPE_NORMAL) == Db::JURY_TYPE_DIRIMENTE) ? true : false;

        $oUser = Jury::where('usuario', '=', $user)->first();

        if ($oUser) {
            if ($oUser->password == $pass) {
                if ($oUser->estado == Db::JURY_DISCONNECTED) {
                    $oCategory = Category::find($category);
                    $lstCatJury = CategoryJury::where('categoria_id', '=', $oCategory->id)->get();
                    $processDirimente = true;

                    if ($jDirimente) {
                        $oCatJuryDirimente = $lstCatJury->filter(function ($item) {
                            return $item->dirimente == Db::JURY_TYPE_DIRIMENTE;
                        })->first();

                        if ($oCatJuryDirimente && $oCatJuryDirimente->jurado_id != $oUser->id) {
                            $processDirimente = false;
                        }
                    }

                    if ($processDirimente) {
                        $oCatJury = $lstCatJury->filter(function ($item) use ($oUser) {
                            return $item->jurado_id == $oUser->id;
                        })->first();

                        if (!$oCatJury) {
                            $oCatJuryNew = CategoryJury::create([
                                'jurado_id' => $oUser->id,
                                'categoria_id' => $oCategory->id,
                                'dirimente' => ($jDirimente) ? Db::JURY_TYPE_DIRIMENTE : Db::JURY_TYPE_NORMAL
                            ]);
                        }

                        $oUser->estado = Db::JURY_CONNECTED;
                        $oUser->save();

                        $authUser = new GenericUser($oUser->toArray());
                        $this->auth->login($authUser);

                        $request->session()->put('category', $oCategory);
                        $request->session()->put('dirimente', $jDirimente);

                        $response['success'] = true;
                        $response['url'] = $this->redirectPath();
                    } else {
                        $response['message'] = 'Ya existe un juez dirimente para esta categoría.';
                    }
                } else {
                    $response['message'] = 'El usuario ya se encuentra conectado, comuniquese con el administrador del sistema.';
                }
            } else {
                $response['message'] = 'Ha ocurrido un error, vuelva a intentarlo.';
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
        $oUser = Jury::find($this->auth->user()->id);
        $oUser->estado = Db::JURY_DISCONNECTED;
        $oUser->save();

        $this->auth->logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

}
