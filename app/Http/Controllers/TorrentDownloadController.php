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

use App\Helpers\Bencode;
use App\Models\Torrent;
use App\Models\TorrentDownload;
use App\Models\User;
use Illuminate\Http\Request;

class TorrentDownloadController extends Controller
{
    /**
     * Download Check.
     */
    public function show(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        $user = $request->user();

        return view('torrent.download_check', ['torrent' => $torrent, 'user' => $user]);
    }

    /**
     * Download A Torrent.
     */
    public function store(Request $request, int $id, $rsskey = null): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user();
        if (! $user && $rsskey) {
            $user = User::where('rsskey', '=', $rsskey)->firstOrFail();
        }
        $torrent = Torrent::withAnyStatus()->findOrFail($id);
        $hasHistory = $user->history()->where([['torrent_id', '=', $torrent->id], ['seeder', '=', 1]])->exists();
        // User's ratio is too low
        if ($user->getRatio() < config('other.ratio') && ! ($torrent->user_id === $user->id || $hasHistory)) {
            return to_route('torrent', ['id' => $torrent->id])
                ->withErrors('Your Ratio Is Too Low To Download!');
        }

        // User's download rights are revoked
        if ($user->can_download == 0 && ! ($torrent->user_id === $user->id || $hasHistory)) {
            return to_route('torrent', ['id' => $torrent->id])
                ->withErrors('Your Download Rights Have Been Revoked!');
        }

        // Torrent Status Is Rejected
        if ($torrent->isRejected()) {
            return to_route('torrent', ['id' => $torrent->id])
                ->withErrors('This Torrent Has Been Rejected By Staff');
        }

        // The torrent file exist ?
        if (! file_exists(getcwd().'/files/torrents/'.$torrent->file_name)) {
            return to_route('torrent', ['id' => $torrent->id])
                ->withErrors('Torrent File Not Found! Please Report This Torrent!');
        }

        if (! $request->user() && !($rsskey && $user)) {
            return to_route('login');
        }

        $torrentDownload = new TorrentDownload();
        $torrentDownload->user_id = $user->id;
        $torrentDownload->torrent_id = $id;
        $torrentDownload->type = $rsskey ? 'RSS/API using '.$request->header('User-Agent') : 'Site using '.$request->header('User-Agent');
        $torrentDownload->save();

        return response()->streamDownload(
            function () use ($id, $user, $torrent): void {
                $dict = Bencode::bdecode(file_get_contents(getcwd().'/files/torrents/'.$torrent->file_name));

                // Set the announce key and add the user passkey
                $dict['announce'] = route('announce', ['passkey' => $user->passkey]);

                // Set link to torrent as the comment
                if (config('torrent.comment')) {
                    $dict['comment'] = config('torrent.comment').'. '.route('torrent', ['id' => $id]);
                } else {
                    $dict['comment'] = route('torrent', ['id' => $id]);
                }

                echo Bencode::bencode($dict);
            },
            str_replace([' ', '/', '\\'], ['.', '-', '-'], '['.config('torrent.source').']'.$torrent->name.'.torrent'),
            ['Content-Type' => 'application/x-bittorrent']
        );
    }
}
