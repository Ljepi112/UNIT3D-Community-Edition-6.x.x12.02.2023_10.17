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
use App\Models\User;

/**
 * @see \Tests\Feature\Http\Controllers\AchievementsControllerTest
 */
class AchievementsController extends Controller
{
    /**
     * Display User Achievements.
     */
    public function index(User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $achievements = $user->unlockedAchievements();
        $pending = $user->inProgressAchievements();

        return view('user.achievement.index', [
            'route'        => 'achievement',
            'user'         => $user,
            'achievements' => $achievements,
            'pending'      => $pending,
        ]);
    }
}
