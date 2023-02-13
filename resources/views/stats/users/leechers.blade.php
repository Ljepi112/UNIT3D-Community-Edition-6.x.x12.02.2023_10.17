@extends('layout.default')

@section('title')
    <title>{{ __('stat.stats') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('stats') }}" class="breadcrumb__link">
            {{ __('stat.stats') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.users') }}
    </li>
@endsection

@section('nav-tabs')
    @include('partials.statsusermenu')
@endsection

@section('page', 'page__stats--leechers')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.top-leechers') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('common.user') }}</th>
                        <th>{{ __('torrent.leeching') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leechers as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <x-user_tag :user="$user->user" :anon="$user->user->private_profile" />
                            </td>
                            <td>{{ $user->user->leechingTorrents()->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
