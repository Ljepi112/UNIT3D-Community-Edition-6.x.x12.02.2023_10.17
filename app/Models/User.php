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

use App\Helpers\Bbcode;
use App\Helpers\Linkify;
use App\Helpers\StringHelper;
use App\Traits\UsersOnlineTrait;
use Assada\Achievements\Achiever;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use voku\helper\AntiXSS;

class User extends Authenticatable
{
    use Achiever;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use UsersOnlineTrait;

    /**
     * The Attributes Excluded From The Model's JSON Form.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'password',
        'passkey',
        'rsskey',
        'remember_token',
        'api_token',
    ];

    /**
     * The Attributes That Should Be Mutated To Dates.
     *
     * @var array
     */
    protected $casts = [
        'last_login'  => 'datetime',
        'last_action' => 'datetime',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Belongs To A Group.
     */
    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class)->withDefault([
            'color'        => config('user.group.defaults.color'),
            'effect'       => config('user.group.defaults.effect'),
            'icon'         => config('user.group.defaults.icon'),
            'name'         => config('user.group.defaults.name'),
            'slug'         => config('user.group.defaults.slug'),
            'position'     => config('user.group.defaults.position'),
            'is_admin'     => config('user.group.defaults.is_admin'),
            'is_freeleech' => config('user.group.defaults.is_freeleech'),
            'is_immune'    => config('user.group.defaults.is_immune'),
            'is_incognito' => config('user.group.defaults.is_incognito'),
            'is_internal'  => config('user.group.defaults.is_internal'),
            'is_modo'      => config('user.group.defaults.is_modo'),
            'is_trusted'   => config('user.group.defaults.is_trusted'),
            'can_upload'   => config('user.group.defaults.can_upload'),
            'level'        => config('user.group.defaults.level'),
        ]);
    }

    /**
     * Belongs To A Internal Group.
     */
    public function internal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internal::class, 'internal_id', 'id', 'name');
    }

    /**
     * Belongs To A Chatroom.
     */
    public function chatroom(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Chatroom::class);
    }

    /**
     * Belongs To A Chat Status.
     */
    public function chatStatus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChatStatus::class, 'chat_status_id', 'id');
    }

    /**
     * Belongs To Many Bookmarks.
     */
    public function bookmarks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'bookmarks', 'user_id', 'torrent_id')->withTimestamps();
    }

    public function isBookmarked(int $torrentId): bool
    {
        return $this->bookmarks()->where('torrent_id', '=', $torrentId)->first() !== null;
    }

    /**
     * Belongs To Many Seeding Torrents.
     */
    public function seedingTorrents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'history')
            ->wherePivot('active', '=', 1)
            ->wherePivot('seeder', '=', 1);
    }

    /**
     * Belongs To Many Leeching Torrents.
     */
    public function leechingTorrents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'history')
            ->wherePivot('active', '=', 1)
            ->wherePivot('seeder', '=', 0);
    }

    /**
     * Belongs to many followers.
     */
    public function followers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'target_id', 'user_id')
            ->as('follow')
            ->withTimestamps();
    }

    /**
     * Belongs to many followees.
     */
    public function following(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'target_id')
            ->as('follow')
            ->withTimestamps();
    }

    /**
     * Has Many Messages.
     */
    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Has One Privacy Object.
     */
    public function privacy(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserPrivacy::class);
    }

    /**
     * Has One Chat Object.
     */
    public function chat(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserChat::class);
    }

    /**
     * Has One Notifications Object.
     */
    public function notification(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserNotification::class);
    }

    /**
     * Has Many RSS Feeds.
     */
    public function rss(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rss::class);
    }

    /**
     * Has Many Echo Settings.
     */
    public function echoes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserEcho::class);
    }

    /**
     * Has Many Audible Settings.
     */
    public function audibles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserAudible::class);
    }

    /**
     * Has Many Thanks Given.
     */
    public function thanksGiven(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Thank::class, 'user_id', 'id');
    }

    /**
     * Has Many Wish's.
     */
    public function wishes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wish::class);
    }

    /**
     * Has Many Thanks Received.
     */
    public function thanksReceived(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Thank::class, Torrent::class);
    }

    /**
     * Has Many Polls.
     */
    public function polls(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Poll::class);
    }

    /**
     * Has Many Torrents.
     */
    public function torrents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Has Many Playlist.
     */
    public function playlists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Has Many Sent PM's.
     */
    public function pm_sender(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }

    /**
     * Has Many Received PM's.
     */
    public function pm_receiver(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrivateMessage::class, 'receiver_id');
    }

    /**
     * Has Many Peers.
     */
    public function peers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Peer::class);
    }

    /**
     * Has Many Articles.
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Has Many Topics.
     */
    public function topics(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Topic::class, 'first_post_user_id', 'id');
    }

    /**
     * Has Many Posts.
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Has Many Comments.
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Has Many Torrent Requests.
     */
    public function requests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequest::class);
    }

    /**
     * Has Approved Many Torrent Requests.
     */
    public function ApprovedRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequest::class, 'approved_by');
    }

    /**
     * Has Filled Many Torrent Requests.
     */
    public function FilledRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequest::class, 'filled_by');
    }

    /**
     * Has Many Torrent Request BON Bounties.
     */
    public function requestBounty(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequestBounty::class);
    }

    /**
     * Has Moderated Many Torrents.
     */
    public function moderated(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Torrent::class, 'moderated_by');
    }

    /**
     * Has Many Notes.
     */
    public function notes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    /**
     * Has Many Reports.
     */
    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Has Solved Many Reports.
     */
    public function solvedReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Report::class, 'staff_id');
    }

    /**
     * Has Many Torrent History.
     */
    public function history(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(History::class, 'user_id');
    }

    /**
     * Has Many Bans.
     */
    public function userban(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ban::class, 'owned_by');
    }

    /**
     * Has Given Many Bans.
     */
    public function staffban(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ban::class, 'created_by');
    }

    /**
     * Has Given Many Warnings.
     */
    public function staffwarning(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Warning::class, 'warned_by');
    }

    /**
     * Has Deleted Many Warnings.
     */
    public function staffdeletedwarning(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Warning::class, 'deleted_by');
    }

    /**
     * Has Many Warnings.
     */
    public function userwarning(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Warning::class, 'user_id');
    }

    /**
     * Has Given Many Invites.
     */
    public function sentInvite(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invite::class, 'user_id');
    }

    /**
     * Has Received Many Invites.
     */
    public function receivedInvite(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invite::class, 'accepted_by');
    }

    /**
     * Has Many Featured Torrents.
     */
    public function featuredTorrent(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeaturedTorrent::class);
    }

    /**
     * Has Many Post Likes.
     */
    public function likes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Has Given Many BON Tips.
     */
    public function bonGiven(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BonTransactions::class, 'sender');
    }

    /**
     * Has Received Many BON Tips.
     */
    public function bonReceived(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BonTransactions::class, 'receiver');
    }

    /**
     * Has Many Subscriptions.
     */
    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Has many free leech tokens.
     */
    public function freeleechTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FreeleechToken::class);
    }

    /**
     * Has many warnings.
     */
    public function warnings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Warning::class);
    }

    /**
     * Has Many Tickets.
     */
    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Has Many Personal Freeleeches.
     */
    public function personalFreeleeches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PersonalFreeleech::class);
    }

    /**
     * Get the Users accepts notification as bool.
     */
    public function acceptsNotification(self $sender, self $target, string $group = 'follower', $type = false): bool
    {
        $targetGroup = 'json_'.$group.'_groups';
        if ($sender->id === $target->id) {
            return false;
        }

        if ($sender->group->is_modo || $sender->group->is_admin) {
            return true;
        }

        if ($target->block_notifications && $target->block_notifications == 1) {
            return false;
        }

        if ($target->notification && $type && (! $target->notification->$type)) {
            return false;
        }

        if (\is_array($target->notification?->$targetGroup)) {
            return ! \in_array($sender->group->id, $target->notification->$targetGroup, true);
        }

        return true;
    }

    /**
     * Get the Users allowed answer as bool.
     */
    public function isVisible(self $target, string $group = 'profile', $type = false): bool
    {
        $targetGroup = 'json_'.$group.'_groups';
        $sender = auth()->user();
        if ($sender->id == $target->id) {
            return true;
        }

        if ($sender->group->is_modo || $sender->group->is_admin) {
            return true;
        }

        if ($target->hidden && $target->hidden == 1) {
            return false;
        }

        if ($target->privacy && $type && (! $target->privacy->$type || $target->privacy->$type == 0)) {
            return false;
        }

        if (\is_array($target->privacy?->$targetGroup)) {
            return ! \in_array($sender->group->id, $target->privacy?->$targetGroup);
        }

        return true;
    }

    /**
     * Get the Users allowed answer as bool.
     */
    public function isAllowed(self $target, string $group = 'profile', $type = false): bool
    {
        $targetGroup = 'json_'.$group.'_groups';
        $sender = auth()->user();
        if ($sender->id == $target->id) {
            return true;
        }

        if ($sender->group->is_modo || $sender->group->is_admin) {
            return true;
        }

        if ($target->private_profile && $target->private_profile == 1) {
            return false;
        }

        if ($target->privacy && $type && (! $target->privacy->$type || $target->privacy->$type == 0)) {
            return false;
        }

        if (\is_array($target->privacy?->$targetGroup)) {
            return ! \in_array($sender->group->id, $target->privacy?->$targetGroup);
        }

        return true;
    }

    /**
     * Return Upload In Human Format.
     */
    public function getUploaded(): string
    {
        $bytes = $this->uploaded;

        if ($bytes > 0) {
            return StringHelper::formatBytes((float) $bytes, 2);
        }

        return StringHelper::formatBytes(0, 2);
    }

    /**
     * Return Download In Human Format.
     */
    public function getDownloaded(): string
    {
        $bytes = $this->downloaded;

        if ($bytes > 0) {
            return StringHelper::formatBytes((float) $bytes, 2);
        }

        return StringHelper::formatBytes(0, 2);
    }

    /**
     * Return The Ratio.
     */
    public function getRatio(): float
    {
        if ($this->downloaded === 0) {
            return INF;
        }

        return round($this->uploaded / $this->downloaded, 2);
    }

    public function getRatioString(): string
    {
        $ratio = $this->getRatio();
        if (is_infinite($ratio)) {
            return '∞';
        }

        return (string) $ratio;
    }

    /**
     * Return the ratio after $size bytes would be downloaded.
     */
    public function ratioAfterSize($size): float
    {
        if ($this->downloaded + $size == 0) {
            return INF;
        }

        return round($this->uploaded / ($this->downloaded + $size), 2);
    }

    /**
     * Return the ratio after $size bytes would be downloaded, pretty formatted as string.
     */
    public function ratioAfterSizeString($size, bool $freeleech = false): string
    {
        if ($freeleech) {
            return $this->getRatioString().' ('.trans('torrent.freeleech').')';
        }

        $ratio = $this->ratioAfterSize($size);
        if (is_infinite($ratio)) {
            return '∞';
        }

        return (string) $ratio;
    }

    /**
     * Return the size (pretty formated) which can be safely downloaded
     * without falling under the minimum ratio.
     */
    public function untilRatio($ratio): string
    {
        if ($ratio == 0.0) {
            return '∞';
        }

        $bytes = round(($this->uploaded / $ratio) - $this->downloaded);

        return StringHelper::formatBytes($bytes);
    }

    /**
     * Set The Users Signature After Its Been Purified.
     */
    public function setSignatureAttribute(?string $value): void
    {
        $this->attributes['signature'] = htmlspecialchars((new AntiXSS())->xss_clean($value), ENT_NOQUOTES);
    }

    /**
     * Returns the HTML of the user's signature.
     */
    public function getSignature(): string
    {
        $bbcode = new Bbcode();

        return (new Linkify())->linky($bbcode->parse($this->signature));
    }

    /**
     * Set The Users About Me After Its Been Purified.
     */
    public function setAboutAttribute(?string $value): void
    {
        $this->attributes['about'] = htmlspecialchars((new AntiXSS())->xss_clean($value), ENT_NOQUOTES);
    }

    /**
     * Parse About Me And Return Valid HTML.
     */
    public function getAboutHtml(): string
    {
        if (empty($this->about)) {
            return 'N/A';
        }

        $bbcode = new Bbcode();

        return (new Linkify())->linky($bbcode->parse($this->about));
    }

    /**
     * @method getSeedbonus
     *
     * Formats the seebonus of the User
     */
    public function getSeedbonus(): string
    {
        return number_format($this->seedbonus, 0, '.', ',');
    }
}
