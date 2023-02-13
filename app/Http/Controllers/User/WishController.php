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
use App\Interfaces\WishInterface;
use App\Models\User;
use App\Services\Tmdb\Client\Movie;
use Illuminate\Http\Request;
use JsonException;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\WishControllerTest
 */
class WishController extends Controller
{
    /**
     * WishController Constructor.
     */
    public function __construct(private readonly WishInterface $wish)
    {
    }

    /**
     * Get A Users Wishlist.
     */
    public function index(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::with('wishes')->where('username', '=', $username)->firstOrFail();

        abort_unless(($request->user()->group->is_modo || $request->user()->id == $user->id), 403);

        $wishes = $user->wishes()->latest()->paginate(25);

        return view('user.wish.index', [
            'user'   => $user,
            'wishes' => $wishes,
            'route'  => 'wish',
        ]);
    }

    /**
     * Add New Wish.
     *
     * @throws JsonException
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        if ($request->get('tmdb') === 0) {
            return to_route('wishes.index', ['username' => $user->username])
                ->withErrors('TMDB ID Required');
        }

        $tmdb = $request->get('tmdb');

        if ($this->wish->exists($user->id, $tmdb)) {
            return to_route('wishes.index', ['username' => $user->username])
                ->withErrors('Wish already exists!');
        }

        $meta = (new Movie($tmdb))->getData();

        if ($meta === null || $meta === false) {
            return to_route('wishes.index', ['username' => $user->username])
                ->withErrors('TMDM Bad Request!');
        }

        $source = $this->wish->getSource($tmdb);

        $this->wish->create([
            'title'   => $meta['title'].' ('.$meta['release_date'].')',
            'type'    => 'Movie',
            'tmdb'    => $tmdb,
            'source'  => $source,
            'user_id' => $user->id,
        ]);

        return to_route('wishes.index', ['username' => $user->username])
            ->withSuccess('Wish Successfully Added!');
    }

    /**
     * Delete A Wish.
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $this->wish->delete($id);

        return to_route('wishes.index', ['username' => $user->username])
            ->withSuccess('Wish Successfully Removed!');
    }
}
