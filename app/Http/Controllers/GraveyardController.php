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

namespace App\Http\Controllers;

use App\Models\Graveyard;
use App\Models\Torrent;
use Illuminate\Http\Request;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\GraveyardControllerTest
 */
class GraveyardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('graveyard.index');
    }

    /**
     * Resurrect A Torrent.
     */
    public function store(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $torrent = Torrent::findOrFail($id);
        $resurrected = Graveyard::where('torrent_id', '=', $torrent->id)->first();

        if ($resurrected) {
            return to_route('graveyard.index')
                ->withErrors(trans('graveyard.resurrect-failed-pending'));
        }

        if ($user->id === $torrent->user_id) {
            return to_route('graveyard.index')
                ->withErrors(trans('graveyard.resurrect-failed-own'));
        }

        $graveyard = new Graveyard();
        $graveyard->user_id = $user->id;
        $graveyard->torrent_id = $torrent->id;
        $graveyard->seedtime = $request->input('seedtime');

        $v = validator($graveyard->toArray(), [
            'user_id'    => 'required',
            'torrent_id' => 'required',
            'seedtime'   => 'required',
        ]);

        if ($v->fails()) {
            return to_route('graveyard.index')
                ->withErrors($v->errors());
        }

        $graveyard->save();

        return to_route('graveyard.index')
            ->withSuccess(trans('graveyard.resurrect-complete'));
    }

    /**
     * Cancel A Ressurection.
     *
     *
     * @throws Exception
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $resurrection = Graveyard::findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $resurrection->user_id, 403);
        $resurrection->delete();

        return to_route('graveyard.index')
            ->withSuccess(trans('graveyard.resurrect-canceled'));
    }
}
