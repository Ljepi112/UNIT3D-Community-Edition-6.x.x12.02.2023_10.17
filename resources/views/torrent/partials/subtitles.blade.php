<div class="panel panel-chat shoutbox torrent-subtitles">
    <div class="panel-heading">
        <h4>
            <i class="{{ config("other.font-awesome") }} fa-closed-captioning"></i> {{ __('common.subtitles') }}
            <a href="{{ route('subtitles.create', ['torrent_id' => $torrent->id]) }}" class="btn btn-xs btn-primary"
               style="float: right;"
               title="{{ __('common.add') }} {{ __('common.subtitle') }}">{{ __('common.add') }} {{ __('common.subtitle') }}</a>
        </h4>
    </div>

    <div class="table-responsive">
        <table class="table table-condensed table-bordered table-striped">
            <thead>
            <tr>
                <th>{{ __('common.language') }}</th>
                <th>{{ __('common.download') }}</th>
                <th>{{ __('subtitle.extension') }}</th>
                <th>{{ __('subtitle.size') }}</th>
                <th>{{ __('subtitle.downloads') }}</th>
                <th>{{ __('subtitle.uploaded') }}</th>
                <th>{{ __('subtitle.uploader') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($torrent->subtitles as $subtitle)
                <tr>
                    <td>
                        {{ $subtitle->language->name }}
                        <i class="{{ config("other.font-awesome") }} fa-closed-captioning"
                           title="{{ $subtitle->note }}"></i>
                    </td>
                    <td>
                        <a href="{{ route('subtitles.download', ['id' => $subtitle->id]) }}"
                           class="btn btn-xs btn-warning">{{ __('common.download') }}</a>
                    </td>
                    <td>{{ $subtitle->extension }}</td>
                    <td>{{ $subtitle->getSize() }}</td>
                    <td>{{ $subtitle->downloads }}</td>
                    <td>{{ $subtitle->created_at->diffForHumans() }}</td>
                    <td>
                        @if ($subtitle->anon == true)
                            <span class="badge-user text-orange text-bold">{{ strtoupper(__('common.anonymous')) }}
                                @if (auth()->user()->id == $subtitle->user_id || auth()->user()->group->is_modo)
                                    <a href="{{ route('users.show', ['username' => $subtitle->user->username]) }}">
                                    ({{ $subtitle->user->username }})
                                </a>
                                @endif
                        </span>
                        @else
                            <a href="{{ route('users.show', ['username' => $subtitle->user->username]) }}">
                            <span class="badge-user text-bold"
                                  style="color:{{ $subtitle->user->group->color }}; background-image:{{ $subtitle->user->group->effect }};">
                                <i class="{{ $subtitle->user->group->icon }}"
                                   title="{{ $subtitle->user->group->name }}"></i> {{ $subtitle->user->username }}
                            </span>
                            </a>
                        @endif

                        @if(auth()->user()->group->is_modo || auth()->user()->id == $subtitle->user->id)
                            <div class="align-right" style="display: inline-block;">
                                @include('subtitle.modals', ['subtitle' => $subtitle, 'torrent' => $torrent, 'media_languages' => App\Models\MediaLanguage::all()->sortBy('name')])
                                <a data-toggle="modal" data-target="#modal_edit_subtitle-{{ $subtitle->id }}"
                                   title="{{ __('common.edit') }} {{ __('common.subtitle') }}"><i
                                            class="fa fa-edit text-green"></i></a>
                                <a data-toggle="modal" data-target="#modal_delete_subtitle-{{ $subtitle->id }}"
                                   title="{{ __('common.delete') }} {{ __('common.subtitle') }}"><i
                                            class="fa fa-trash text-red"></i></a>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="panel-footer text-center">
        @if (count($torrent->subtitles) === 0)
            <div class="text-center">
                <h4 class="text-bold text-danger">
                    <i class="{{ config('other.font-awesome') }} fa-frown"></i> No External Subtitles Availible
                </h4>
            </div>
        @endif
    </div>
</div>
