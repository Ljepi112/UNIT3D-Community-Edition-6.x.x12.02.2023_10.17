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

namespace App\Http\Livewire;

use App\Models\TorrentRequest;
use App\Models\TorrentRequestBounty;
use App\Models\TorrentRequestClaim;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TorrentRequestSearch extends Component
{
    use WithPagination;

    public string $name = '';

    public string $requestor = '';

    public array $categories = [];

    public array $types = [];

    public array $resolutions = [];

    public array $genres = [];

    public string $tmdbId = '';

    public string $imdbId = '';

    public string $tvdbId = '';

    public string $malId = '';

    public $unfilled;

    public $claimed;

    public $pending;

    public $filled;

    public $myRequests;

    public $myClaims;

    public $myVoted;

    public $myFilled;

    public int $perPage = 25;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public bool $showFilters = false;

    protected $queryString = [
        'name'          => ['except' => ''],
        'requestor'     => ['except' => ''],
        'categories'    => ['except' => []],
        'types'         => ['except' => []],
        'resolutions'   => ['except' => []],
        'genres'        => ['except' => []],
        'tmdbId'        => ['except' => ''],
        'imdbId'        => ['except' => ''],
        'tvdbId'        => ['except' => ''],
        'malId'         => ['except' => ''],
        'unfilled'      => ['except' => false],
        'claimed'       => ['except' => false],
        'pending'       => ['except' => false],
        'filled'        => ['except' => false],
        'myRequests'    => ['except' => false],
        'myClaims'      => ['except' => false],
        'myVoted'       => ['except' => false],
        'myFilled'      => ['except' => false],
        'sortField'     => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page'          => ['except' => 1],
    ];

    final public function paginationView(): string
    {
        return 'vendor.pagination.livewire-pagination';
    }

    final public function updatedPage(): void
    {
        $this->emit('paginationChanged');
    }

    final public function toggleShowFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
    }

    final public function getTorrentRequestStatProperty(): ?object
    {
        return DB::table('requests')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when filled_by is not null then 1 end) as filled')
            ->selectRaw('count(case when filled_by is null then 1 end) as unfilled')
            ->first();
    }

    final public function getTorrentRequestBountyStatProperty(): ?object
    {
        return DB::table('requests')
            ->selectRaw('coalesce(sum(bounty), 0) as total')
            ->selectRaw('coalesce(sum(case when filled_by is not null then bounty end), 0) as claimed')
            ->selectRaw('coalesce(sum(case when filled_by is null then bounty end), 0) as unclaimed')
            ->first();
    }

    final public function getTorrentRequestsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return TorrentRequest::with(['category', 'type', 'resolution'])
            ->withCount(['comments'])
            ->when($this->name, function ($query): void {
                $query->where('name', 'LIKE', '%'.$this->name.'%');
            })
            ->when($this->requestor, function ($query): void {
                $match = User::where('username', 'LIKE', '%'.$this->requestor.'%')->oldest('username')->first();
                if ($match) {
                    $query
                        ->where('user_id', '=', $match->id)
                        ->when(! (auth()->user()->group->is_modo || auth()->user()->id === $match->id), function ($query): void {
                            $query->where('anon', '=', 0);
                        });
                }
            })
            ->when($this->categories, function ($query): void {
                $query->whereIntegerInRaw('category_id', $this->categories);
            })
            ->when($this->types, function ($query): void {
                $query->whereIntegerInRaw('type_id', $this->types);
            })
            ->when($this->resolutions, function ($query): void {
                $query->whereIntegerInRaw('resolution_id', $this->resolutions);
            })
            ->when($this->tmdbId, function ($query): void {
                $query->where('tmdb', '=', $this->tmdbId);
            })
            ->when($this->imdbId, function ($query): void {
                if (preg_match('/tt0*?(?=(\d{7,8}))/', $this->imdbId, $matches)) {
                    $query->where('imdb', '=', $matches[1]);
                } else {
                    $query->where('imdb', '=', $this->imdbId);
                }
            })
            ->when($this->tvdbId, function ($query): void {
                $query->where('tvdb', '=', $this->tvdbId);
            })
            ->when($this->malId, function ($query): void {
                $query->where('mal', '=', $this->malId);
            })
            ->when($this->unfilled || $this->claimed || $this->pending || $this->filled, function ($query): void {
                $query->where(function ($query): void {
                    $query->where(function ($query): void {
                        if ($this->unfilled) {
                            $query->whereNull('torrent_id')->whereNull('claimed');
                        }
                    })
                        ->orWhere(function ($query): void {
                            if ($this->claimed) {
                                $query->whereNotNull('claimed')->whereNull('torrent_id')->whereNull('approved_by');
                            }
                        })
                        ->orWhere(function ($query): void {
                            if ($this->pending) {
                                $query->whereNotNull('torrent_id')->whereNull('approved_by');
                            }
                        })
                        ->orWhere(function ($query): void {
                            if ($this->filled) {
                                $query->whereNotNull('torrent_id')->whereNotNull('approved_by');
                            }
                        });
                });
            })
            ->when($this->myRequests, function ($query): void {
                $query->where('user_id', '=', auth()->user()->id);
            })
            ->when($this->myClaims, function ($query): void {
                $requestClaims = TorrentRequestClaim::where('username', '=', auth()->user()->username)->pluck('request_id');
                $query->whereIntegerInRaw('id', $requestClaims)->whereNull('torrent_id')->whereNull('approved_by');
            })
            ->when($this->myVoted, function ($query): void {
                $requestVotes = TorrentRequestBounty::where('user_id', '=', auth()->user()->id)->pluck('requests_id');
                $query->whereIntegerInRaw('id', $requestVotes);
            })
            ->when($this->myFilled, function ($query): void {
                $query->where('filled_by', '=', auth()->user()->id);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.torrent-request-search', [
            'user'                     => auth()->user(),
            'torrentRequests'          => $this->torrentRequests,
            'torrentRequestStat'       => $this->torrentRequestStat,
            'torrentRequestBountyStat' => $this->torrentRequestBountyStat,
        ]);
    }
}
