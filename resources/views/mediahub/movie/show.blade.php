@extends('layout.default')

@section('title')
    <title>{{ $movie->title }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="">
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.movies.index') }}" class="breadcrumb__link">
            {{ __('mediahub.movies') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $movie->title }}
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block single">
            @include('torrent.partials.movie_meta', ['torrent' => $movie->torrents->first(), 'meta' => $movie])

            <div class="table-responsive mt-20">
                <table class="table table-condensed table-bordered table-striped">
                    @foreach(App\Models\Type::all()->sortBy('position') as $type)
                        @if($movie->torrents->where('type_id', '=', $type->id)->count() > 0)
                            <thead>
                            <tr>
                                <th class="text-center dark-th" colspan="6">
                                    <span style="font-size: 16px;">{{ $type->name }}</span>
                                </th>
                            </tr>
                            </thead>
                        @endif
                        @foreach(App\Models\Resolution::all()->sortBy('position') as $resolution)
                            @if($movie->torrents->where('type_id', '=', $type->id)->where('resolution_id', '=', $resolution->id)->count() > 0)
                                <thead>
                                <tr>
                                    <th colspan="6">
                                        <span class="label label-default text-bold">{{ $resolution->name }}</span>
                                    </th>
                                </tr>
                                </thead>
                            @endif
                            <tbody>
                            @foreach($movie->torrents->where('type_id', '=', $type->id)->where('resolution_id', '=', $resolution->id)->sortByDesc('created_at') as $torrent)
                                @if($torrent->sticky == 1)
                                    <tr class="success">
                                @else
                                    <tr>
                                        @endif
                                        <td>
                                            <a class="text-bright" style="font-size: 16px;"
                                               href="{{ route('torrent', ['id' => $torrent->id]) }}">
                                                {{ $torrent->name }}
                                            </a>
                                            @if (config('torrent.download_check_page') == 1)
                                                <a href="{{ route('download_check', ['id' => $torrent->id]) }}">
                                                    <button class="btn btn-primary btn-circle" type="button"
                                                            title="{{ __('common.download') }}">
                                                        <i class="{{ config('other.font-awesome') }} fa-download"></i>
                                                    </button>
                                                </a>
                                            @else
                                                <a href="{{ route('download', ['id' => $torrent->id]) }}">
                                                    <button class="btn btn-primary btn-circle" type="button"
                                                            title="{{ __('common.download') }}">
                                                        <i class="{{ config('other.font-awesome') }} fa-download"></i>
                                                    </button>
                                                </a>
                                            @endif

                                            @php $history = App\Models\History::where('user_id', '=', $user->id)->where('torrent_id', '=', $torrent->id)->first() @endphp
                                            @if ($history)
                                                @if ($history->seeder == 1 && $history->active == 1)
                                                    <button class="btn btn-success btn-circle" type="button"
                                                            title="{{ __('torrent.currently-seeding') }}!">
                                                        <i class="{{ config('other.font-awesome') }} fa-arrow-up"></i>
                                                    </button>
                                                @endif

                                                @if ($history->seeder == 0 && $history->active == 1)
                                                    <button class="btn btn-warning btn-circle" type="button"
                                                            title="{{ __('torrent.currently-leeching') }}!">
                                                        <i class="{{ config('other.font-awesome') }} fa-arrow-down"></i>
                                                    </button>
                                                @endif

                                                @if ($history->seeder == 0 && $history->active == 0 && $history->completed_at == null)
                                                    <button class="btn btn-info btn-circle" type="button"
                                                            title="{{ __('torrent.not-completed') }}!">
                                                        <i class="{{ config('other.font-awesome') }} fa-spinner"></i>
                                                    </button>
                                                @endif

                                                @if ($history->seeder == 0 && $history->active == 0 && $history->completed_at != null)
                                                    <button class="btn btn-danger btn-circle" type="button"
                                                            title="{{ __('torrent.completed-not-seeding') }}!">
                                                        <i class="{{ config('other.font-awesome') }} fa-thumbs-down"></i>
                                                    </button>
                                                @endif
                                            @endif

                                            <br>
                                            @if ($torrent->anon == 1)
                                                <span class="badge-extra">
                                            <i class="{{ config('other.font-awesome') }} fa-upload"
                                               title="{{ __('torrent.uploader') }}"></i> {{ __('common.anonymous') }}
                                                    @if ($user->id == $torrent->user->id || $user->group->is_modo)
                                                        <a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                                    ({{ $torrent->user->username }})
                                                </a>
                                                    @endif
                                        </span>
                                            @else
                                                <span class="badge-extra">
                                            <i class="{{ config('other.font-awesome') }} fa-upload"
                                               title="{{ __('torrent.uploader') }}"></i>
                                            <a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                                {{ $torrent->user->username }}
                                            </a>
                                        </span>
                                            @endif

                                            <span class="badge-extra text-pink">
                                            <i class="{{ config('other.font-awesome') }} fa-heart"
                                               title="{{ __('torrent.thanks-given') }}"></i>
                                            {{ $torrent->thanks_count }}
                                        </span>

                                            <span class="badge-extra text-green">
                                            <i class="{{ config('other.font-awesome') }} fa-comment"
                                               title="{{ __('common.comments') }}"></i>
                                            {{ $torrent->comments_count }}
                                        </span>

                                            @if ($torrent->internal == 1)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-magic'
                                                   title='{{ __('torrent.internal-release') }}'
                                                   style="color: #baaf92;"></i>
                                            </span>
                                            @endif

                                            @if ($torrent->stream == 1)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-play text-red'
                                                   title='{{ __('torrent.stream-optimized') }}'></i>
                                            </span>
                                            @endif

                                            @if ($torrent->featured == 0)
                                                @if ($torrent->doubleup == 1)
                                                    <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-gem text-green'
                                                       title='{{ __('torrent.double-upload') }}'></i>
                                                </span>
                                                @endif
                                                @if ($torrent->free == 1)
                                                    <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-star text-gold'
                                                       title='{{ __('torrent.freeleech') }}'></i>
                                                </span>
                                                @endif
                                            @endif

                                            @if ($personal_freeleech)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-id-badge text-orange'
                                                   title='{{ __('torrent.personal-freeleech') }}'></i>
                                            </span>
                                            @endif

                                            @php $freeleech_token = \cache()->get('freeleech_token:'.$user->id.':'.$torrent->id) @endphp
                                            @if ($freeleech_token)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-star'
                                                   title='{{ __('torrent.freeleech-token') }}'></i>
                                            </span>
                                            @endif

                                            @if ($torrent->featured == 1)
                                                <span class='badge-extra'
                                                      style='background-image:url(/img/sparkels.gif);'>
                                                <i class='{{ config('other.font-awesome') }} fa-certificate text-pink'
                                                   title='{{ __('torrent.featured') }}'></i>
                                            </span>
                                            @endif

                                            @if ($user->group->is_freeleech == 1)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-trophy text-purple'
                                                   title='{{ __('torrent.special-freeleech') }}'></i>
                                            </span>
                                            @endif

                                            @if (config('other.freeleech') == 1)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-globe text-blue'
                                                   title='{{ __('torrent.global-freeleech') }}'></i>
                                            </span>
                                            @endif

                                            @if (config('other.doubleup') == 1)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-globe text-green'
                                                   title='{{ __('torrent.double-upload') }}'></i>
                                            </span>
                                            @endif

                                            @if ($user->group->is_double_upload == 1)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-trophy text-purple'
                                                   title='{{ __('torrent.special-double_upload') }}'></i>
                                            </span>
                                            @endif

                                            @if ($torrent->leechers >= 5)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-fire text-orange'
                                                   title='{{ __('common.hot') }}!'></i>
                                            </span>
                                            @endif

                                            @if ($torrent->sticky == 1)
                                                <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-thumbtack text-black'
                                                   title='{{ __('torrent.sticky') }}!'></i>
                                            </span>
                                            @endif

                                            @if ($user->updated_at->getTimestamp() < $torrent->created_at->getTimestamp())
                                                <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-magic text-black'
                                                       title='{{ __('common.new') }}!'></i>
                                                </span>
                                            @endif

                                            @if ($torrent->highspeed == 1)
                                                <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-tachometer text-red'
                                                       title='{{ __('common.high-speeds') }}'></i>
                                                </span>
                                            @endif

                                            @if ($torrent->sd == 1)
                                                <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-ticket text-orange'
                                                       title='{{ __('torrent.sd-content') }}'></i>
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <time>{{ $torrent->created_at->diffForHumans() }}</time>
                                        </td>
                                        <td>
                                            <span class='badge-extra text-blue'>{{ $torrent->getSize() }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('peers', ['id' => $torrent->id]) }}">
                                        <span class='badge-extra text-green'>
                                            {{ $torrent->seeders }}
                                        </span>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('peers', ['id' => $torrent->id]) }}">
                                        <span class='badge-extra text-red'>
                                            {{ $torrent->leechers }}
                                        </span>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('history', ['id' => $torrent->id]) }}">
                                        <span class='badge-extra text-orange'>
                                            {{ $torrent->times_completed }} {{ __('common.times') }}
                                        </span>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        @endforeach
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
