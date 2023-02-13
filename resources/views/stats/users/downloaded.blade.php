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

@section('page', 'page__stats--downloaded')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.top-downloaders') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('common.user') }}</th>
                        <th>{{ __('common.upload') }}</th>
                        <th>{{ __('common.download') }}</th>
                        <th>{{ __('common.ratio') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($downloaded as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <x-user_tag :user="$user" :anon="$user->private_profile" />
                            </td>
                            <td>{{ \App\Helpers\StringHelper::formatBytes($user->uploaded, 2) }}</td>
                            <td>{{ \App\Helpers\StringHelper::formatBytes($user->downloaded, 2) }}</td>
                            <td>{{ $user->getRatio() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
