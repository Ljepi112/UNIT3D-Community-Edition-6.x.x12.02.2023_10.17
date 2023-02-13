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
use App\Models\PrivateMessage;
use App\Models\User;
use App\Models\Warning;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\WarningControllerTest
 */
class WarningController extends Controller
{
    /**
     * Show A Users Warnings.
     */
    public function show(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->group->is_modo, 403);

        $user = User::where('username', '=', $username)->firstOrFail();

        $warnings = Warning::where('user_id', '=', $user->id)->with(['torrenttitle', 'warneduser'])->latest('active')->paginate(25);
        $warningcount = Warning::where('user_id', '=', $user->id)->count();

        $softDeletedWarnings = Warning::where('user_id', '=', $user->id)->with(['torrenttitle', 'warneduser'])->latest('created_at')->onlyTrashed()->paginate(25);
        $softDeletedWarningCount = Warning::where('user_id', '=', $user->id)->onlyTrashed()->count();

        return view('user.warning.index', [
            'warnings'                => $warnings,
            'warningcount'            => $warningcount,
            'softDeletedWarnings'     => $softDeletedWarnings,
            'softDeletedWarningCount' => $softDeletedWarningCount,
            'user'                    => $user,
        ]);
    }

    /**
     * Deactivate A Warning.
     */
    public function deactivate(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);
        $staff = $request->user();
        $warning = Warning::findOrFail($id);
        $warning->expires_on = Carbon::now();
        $warning->active = 0;
        $warning->save();

        // Send Private Message
        $privateMessage = new PrivateMessage();
        $privateMessage->sender_id = $staff->id;
        $privateMessage->receiver_id = $warning->user_id;
        $privateMessage->subject = 'Hit and Run Warning Deactivated';
        $privateMessage->message = $staff->username.' has decided to deactivate your active warning for torrent '.$warning->torrent.' You lucked out! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]';
        $privateMessage->save();

        return to_route('warnings.show', ['username' => $warning->warneduser->username])
            ->withSuccess('Warning Was Successfully Deactivated');
    }

    /**
     * Deactivate All Warnings.
     */
    public function deactivateAllWarnings(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);
        $staff = $request->user();
        $user = User::where('username', '=', $username)->firstOrFail();

        foreach (Warning::where('user_id', '=', $user->id)->get() as $warning) {
            $warning->expires_on = Carbon::now();
            $warning->active = 0;
            $warning->save();
        }

        // Send Private Message
        $privateMessage = new PrivateMessage();
        $privateMessage->sender_id = $staff->id;
        $privateMessage->receiver_id = $user->id;
        $privateMessage->subject = 'All Hit and Run Warning Deactivated';
        $privateMessage->message = $staff->username.' has decided to deactivate all of your active hit and run warnings. You lucked out! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]';
        $privateMessage->save();

        return to_route('warnings.show', ['username' => $user->username])
            ->withSuccess('All Warnings Were Successfully Deactivated');
    }

    /**
     * Delete A Warning.
     *
     *
     * @throws Exception
     */
    public function deleteWarning(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $staff = $request->user();
        $warning = Warning::findOrFail($id);

        // Send Private Message
        $privateMessage = new PrivateMessage();
        $privateMessage->sender_id = $staff->id;
        $privateMessage->receiver_id = $warning->user_id;
        $privateMessage->subject = 'Hit and Run Warning Deleted';
        $privateMessage->message = $staff->username.' has decided to delete your warning for torrent '.$warning->torrent.' You lucked out! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]';
        $privateMessage->save();

        $warning->deleted_by = $staff->id;
        $warning->save();
        $warning->delete();

        return to_route('warnings.show', ['username' => $warning->warneduser->username])
            ->withSuccess('Warning Was Successfully Deleted');
    }

    /**
     * Delete All Warnings.
     */
    public function deleteAllWarnings(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $staff = $request->user();
        $user = User::where('username', '=', $username)->firstOrFail();

        foreach (Warning::where('user_id', '=', $user->id)->get() as $warning) {
            $warning->deleted_by = $staff->id;
            $warning->save();
            $warning->delete();
        }

        // Send Private Message
        $privateMessage = new PrivateMessage();
        $privateMessage->sender_id = $staff->id;
        $privateMessage->receiver_id = $user->id;
        $privateMessage->subject = 'All Hit and Run Warnings Deleted';
        $privateMessage->message = $staff->username.' has decided to delete all of your warnings. You lucked out! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]';
        $privateMessage->save();

        return to_route('warnings.show', ['username' => $user->username])
            ->withSuccess('All Warnings Were Successfully Deleted');
    }

    /**
     * Restore A Soft Deleted Warning.
     */
    public function restoreWarning(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $warning = Warning::withTrashed()->findOrFail($id);
        $warning->restore();

        return to_route('warnings.show', ['username' => $warning->warneduser->username])
            ->withSuccess('Warning Was Successfully Restored');
    }
}
