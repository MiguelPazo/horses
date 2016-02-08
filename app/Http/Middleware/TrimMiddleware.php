<?php namespace Horses\Http\Middleware;

use Closure;

class TrimMiddleware
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
        $request->merge(
            $this->array_map_recursive(
                'trim',
                array_except(
                    $request->all(),
                    ['password', 'password_confirmation']
                )
            )
        );

        return $next($request);
    }

    /**
     * @param callable $callback
     * @param array $array
     *
     * @return mixed
     */
    public function array_map_recursive($callback, $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($array[$key])) {
                $array[$key] = $this->array_map_recursive($callback, $array[$key]);
            } else {
                $array[$key] = str_replace('  ', ' ', call_user_func($callback, $array[$key]));
            }
        }

        return $array;
    }

}
