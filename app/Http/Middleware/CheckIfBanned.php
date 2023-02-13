<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Exception;

class CheckIfBanned
{
    /**
     * Handle an incoming request.
     *
     * @throws Exception
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next, ?string $guard = null): mixed
    {
        $user = $request->user();
        $bannedGroup = cache()->rememberForever('banned_group', fn () => Group::where('slug', '=', 'banned')->pluck('id'));

        if ($user && (is_countable($bannedGroup) ? \count($bannedGroup) : 0) > 0 && $user->group_id === $bannedGroup[0]) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => __('auth.banned'),
                ]);
            }
            auth()->logout();
            $request->session()->flush();

            return to_route('login')
                ->withErrors(__('auth.banned'));
        }

        return $next($request);
    }
}
