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

use App\Traits\Auditable;
use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seedbox extends Model
{
    use Auditable;
    use Encryptable;
    use HasFactory;

    /**
     * The Database Table Used By The Model.
     *
     * @var string
     */
    protected $table = 'clients';

    /**
     * The Attributes That Are Encrypted.
     */
    protected array $encryptable = [
        'ip',
    ];

    /**
     * Belongs To A User.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'System',
            'id'       => '1',
        ]);
    }
}
