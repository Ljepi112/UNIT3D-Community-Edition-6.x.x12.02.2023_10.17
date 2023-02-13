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

namespace App\Http\Controllers\MediaHub;

use App\Http\Controllers\Controller;
use App\Models\Person;

class PersonController extends Controller
{
    /**
     * Display All Persons.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('mediahub.person.index');
    }

    /**
     * Show A Person.
     */
    public function show(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $person = Person::with([
            'tv' => fn ($query) => $query->has('torrents'),
            'tv.genres',
            'movie' => fn ($query) => $query->has('torrents'),
            'movie.genres'
        ])->findOrFail($id);

        return view('mediahub.person.show', ['person' => $person]);
    }
}
