<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

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
if (config('unit3d.proxy_scheme')) {
    URL::forceScheme(config('unit3d.proxy_scheme'));
}
if (config('unit3d.root_url_override')) {
    URL::forceRootUrl(config('unit3d.root_url_override'));
}

Route::group(['before' => 'auth'], function (): void {
    // RSS (RSS Key Auth)
    Route::get('/rss/{id}.{rsskey}', [App\Http\Controllers\RssController::class, 'show'])->name('rss.show.rsskey');
    Route::get('/torrent/download/{id}.{rsskey}', [App\Http\Controllers\TorrentDownloadController::class, 'store'])->name('torrent.download.rsskey');
});
