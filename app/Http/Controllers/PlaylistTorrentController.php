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

use App\Models\Playlist;
use App\Models\PlaylistTorrent;
use Illuminate\Http\Request;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\PlaylistTorrentControllerTest
 */
class PlaylistTorrentController extends Controller
{
    /**
     * Attach A Torrent To A Playlist.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $playlist = Playlist::findOrFail($request->input('playlist_id'));

        abort_unless($user->id === $playlist->user_id, 403);

        $playlistTorrent = new PlaylistTorrent();
        $playlistTorrent->playlist_id = $playlist->id;
        $playlistTorrent->torrent_id = $request->input('torrent_id');

        $v = validator($playlistTorrent->toArray(), [
            'playlist_id' => 'required|numeric|exists:playlists,id|unique:playlist_torrents,playlist_id,NULL,NULL,torrent_id,'.$request->input('torrent_id'),
            'torrent_id'  => 'required|numeric|exists:torrents,id|unique:playlist_torrents,torrent_id,NULL,NULL,playlist_id,'.$request->input('playlist_id'),
        ]);

        if ($v->fails()) {
            return to_route('playlists.show', ['id' => $playlist->id])
                ->withErrors($v->errors());
        }

        $playlistTorrent->save();

        return to_route('playlists.show', ['id' => $playlist->id])
            ->withSuccess(trans('playlist.attached-success'));
    }

    /**
     * Detach A Torrent From A Playlist.
     *
     * @throws Exception
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $playlistTorrent = PlaylistTorrent::findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $playlistTorrent->playlist->user_id, 403);
        $playlistTorrent->delete();

        return to_route('playlists.show', ['id' => $playlistTorrent->playlist->id])
            ->withSuccess(trans('playlist.detached-success'));
    }
}
