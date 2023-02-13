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

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array|\Illuminate\Contracts\Support\Arrayable|JsonSerializable
    {
        return parent::toArray($request);
    }
}
