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

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewUnfollow extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewUnfolllow Constructor.
     */
    public function __construct(public string $type, public User $unfollower)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => $this->unfollower->username.' Has Unfollowed You!',
            'body'  => $this->unfollower->username.' has stopped following you so they will no longer get notifications about your activities.',
            'url'   => '/users/'.$this->unfollower->username,
        ];
    }
}
