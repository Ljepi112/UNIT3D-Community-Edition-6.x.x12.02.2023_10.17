@extends('layout.default')

@section('title')
    <title>{{ $user->username }} - Privacy - {{ __('common.members') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['username' => $user->username]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('users.general_settings.edit', ['user' => $user]) }}" class="breadcrumb__link">
            {{ __('user.settings') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.privacy') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('content')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.privacy') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('users.privacy_settings.update', ['user' => $user]) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PATCH')
                <fieldset class="form__fieldset">
                    <legend class="form__legend">Profile</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_torrent_count" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_torrent_count"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_torrent_count)
                            />
                            {{ __('user.profile-privacy-torrent-count') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_title" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_title"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_title)
                            />
                            {{ __('user.profile-privacy-title') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_about" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_about"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_about)
                            />
                            {{ __('user.profile-privacy-about') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_torrent_ratio" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_torrent_ratio"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_torrent_ratio)
                            />
                            {{ __('user.profile-privacy-torrent-ratio') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_torrent_seed" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_torrent_seed"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_torrent_seed)
                            />
                            {{ __('user.profile-privacy-torrent-seed') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_bon_extra" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_bon_extra"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_bon_extra)
                            />
                            {{ __('user.profile-privacy-bon-extra') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_torrent_extra" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_torrent_extra"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_torrent_extra)
                            />
                            {{ __('user.profile-privacy-torrent-extra') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_comment_extra" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_comment_extra"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_comment_extra)
                            />
                            {{ __('user.profile-privacy-comment-extra') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_request_extra" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_request_extra"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_request_extra)
                            />
                            {{ __('user.profile-privacy-request-extra') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_forum_extra" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_forum_extra"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_forum_extra)
                            />
                            {{ __('user.profile-privacy-forum-extra') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_warning" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_warning"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_warning)
                            />
                            {{ __('user.profile-privacy-warning') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_badge" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_badge"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_badge)
                            />
                            {{ __('user.profile-privacy-badge') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_achievement" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_achievement"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_achievement)
                            />
                            {{ __('user.profile-privacy-achievement') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_profile_follower" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_profile_follower"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_profile_follower)
                            />
                            {{ __('user.profile-privacy-follower') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">Achievements</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_achievement" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_achievement"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_achievement)
                            />
                            {{ __('user.achievement-privacy-list') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">Followers</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_follower" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_follower"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_follower)
                            />
                            {{ __('user.follower-privacy-list') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">Forums</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_topic" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_topic"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_topic)
                            />
                            {{ __('user.forum-privacy-topic') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_post" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_post"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_post)
                            />
                            {{ __('user.forum-privacy-post') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">Requests</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_requested" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_requested"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_requested)
                            />
                            {{ __('user.request-privacy-requested') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">Torrents</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_upload" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_upload"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_upload)
                            />
                            {{ __('user.torrent-privacy-upload') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_download" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_download"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_download)
                            />
                            {{ __('user.torrent-privacy-download') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_peer" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_peer"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_peer)
                            />
                            {{ __('user.torrent-privacy-peer') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">Other</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_online" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_online"
                                value="1"
                                @checked($user->privacy === null || $user->privacy->show_online)
                            />
                            {{ __('user.other-privacy-online') }}
                        </label>
                    </p>
                </fieldset>
                <h3>Hide your profile options from the selected groups.</h3>
                <div class="form__group--short-horizontal">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Profile</legend>
                        @foreach($groups as $group)
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        name="json_profile_groups[]"
                                        value="{{ $group->id }}"
                                        @checked($user->privacy !== null && \in_array($group->id, $user->privacy->json_profile_groups, true))
                                    />
                                    {{ $group->name }}
                                </label>
                            </p>
                        @endforeach
                    </fieldset>
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Achievements</legend>
                        @foreach($groups as $group)
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        name="json_achievement_groups[]"
                                        value="{{ $group->id }}"
                                        @checked($user->privacy !== null && \in_array($group->id, $user->privacy->json_achievement_groups, true))
                                    />
                                    {{ $group->name }}
                                </label>
                            </p>
                        @endforeach
                    </fieldset>
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Followers</legend>
                        @foreach($groups as $group)
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        name="json_follower_groups[]"
                                        value="{{ $group->id }}"
                                        @checked($user->privacy !== null && \in_array($group->id, $user->privacy->json_follower_groups, true))
                                    />
                                    {{ $group->name }}
                                </label>
                            </p>
                        @endforeach
                    </fieldset>
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Forums</legend>
                        @foreach($groups as $group)
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        name="json_forum_groups[]"
                                        value="{{ $group->id }}"
                                        @checked($user->privacy !== null && \in_array($group->id, $user->privacy->json_forum_groups, true))
                                    />
                                    {{ $group->name }}
                                </label>
                            </p>
                        @endforeach
                    </fieldset>
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Requests</legend>
                        @foreach($groups as $group)
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        name="json_request_groups[]"
                                        value="{{ $group->id }}"
                                        @checked($user->privacy !== null && \in_array($group->id, $user->privacy->json_request_groups, true))
                                    />
                                    {{ $group->name }}
                                </label>
                            </p>
                        @endforeach
                    </fieldset>
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Torrents</legend>
                        @foreach($groups as $group)
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        name="json_torrent_groups[]"
                                        value="{{ $group->id }}"
                                        @checked($user->privacy !== null && \in_array($group->id, $user->privacy->json_torrent_groups, true))
                                    />
                                    {{ $group->name }}
                                </label>
                            </p>
                        @endforeach
                    </fieldset>
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Other</legend>
                        @foreach($groups as $group)
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        name="json_other_groups[]"
                                        value="{{ $group->id }}"
                                        @checked($user->privacy !== null && \in_array($group->id, $user->privacy->json_other_groups, true))
                                    />
                                    {{ $group->name }}
                                </label>
                            </p>
                        @endforeach
                    </fieldset>
                </div>
                <p class="form__group">
                    <label class="form__label">
                        <input type="hidden" name="hidden" value="0" />
                        <input
                            class="form__checkbox"
                            type="checkbox"
                            value="1"
                            name="hidden"
                            @checked($user->hidden)
                        />
                        {{ __('user.become-hidden') }}
                    </label>
                </p>
                <p class="form__group">
                    <label class="form__label">
                        <input type="hidden" name="private_profile" value="0" />
                        <input
                            class="form__checkbox"
                            type="checkbox"
                            value="1"
                            name="private_profile"
                            @checked($user->private_profile)
                        />
                        {{ __('user.go-private') }}
                    </label>
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.save') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
