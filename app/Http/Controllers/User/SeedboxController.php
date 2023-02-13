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

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Seedbox;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\SeedboxControllerTest
 */
class SeedboxController extends Controller
{
    /**
     * Get A Users Registered Seedboxes.
     */
    public function index(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        abort_unless(($request->user()->group->is_modo || $request->user()->id == $user->id), 403);

        $seedboxes = Seedbox::where('user_id', '=', $user->id)->paginate(25);

        return view('user.seedbox.index', ['user' => $user, 'seedboxes' => $seedboxes]);
    }

    /**
     * Store A Seedbox.
     */
    protected function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        // The user's seedbox IPs are encrypted, so they have to be decrypted first to check that the new IP inputted is unique
        $userSeedboxes = Seedbox::where('user_id', '=', $user->id)->get(['ip', 'name']);
        $seedboxIps = $userSeedboxes->pluck('ip')->filter(fn ($ip) => filter_var($ip, FILTER_VALIDATE_IP));
        $seedboxNames = $userSeedboxes->pluck('name');

        $v = validator(
            $request->input(),
            [
                'name' => [
                    'required',
                    'alpha_num',
                    Rule::notIn($seedboxNames),
                ],
                'ip' => [
                    'bail',
                    'required',
                    'ip',
                    Rule::notIn($seedboxIps),
                ],
            ],
            [
                'name.not_in' => 'You have already used this seedbox name.',
                'ip.not_in'   => 'You have already registered this seedbox IP.',
            ]
        );

        if ($v->fails()) {
            return to_route('seedboxes.index', ['username' => $user->username])
                ->withErrors($v->errors());
        }

        $validated = $v->validated();

        $seedbox = new Seedbox();
        $seedbox->user_id = $user->id;
        $seedbox->name = $validated['name'];
        $seedbox->ip = $validated['ip'];
        $seedbox->save();

        return to_route('seedboxes.index', ['username' => $user->username])
            ->withSuccess(trans('user.seedbox-added-success'));
    }

    /**
     * Delete A Seedbox.
     *
     * @throws Exception
     */
    protected function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $seedbox = Seedbox::findOrFail($id);

        abort_unless(($user->group->is_modo || $user->id == $seedbox->user_id), 403);

        $seedbox->delete();

        return to_route('seedboxes.index', ['username' => $user->username])
            ->withSuccess(trans('user.seedbox-deleted-success'));
    }
}
