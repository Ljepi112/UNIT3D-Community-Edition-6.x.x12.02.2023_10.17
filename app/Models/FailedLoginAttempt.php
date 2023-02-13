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

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    use HasFactory;

    /**
     * The Attributes That Are Mass Assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'username',
        'ip_address',
    ];

    public static function record($user, $username, $ip): mixed
    {
        return static::create([
            'user_id'    => null === $user ? null : $user->id,
            'username'   => $username,
            'ip_address' => $ip,
        ]);
    }
}
