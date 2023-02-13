@extends('layout.default')

@section('title')
    <title>{{ __('forum.forums') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ config('other.title') }} - {{ __('forum.forums') }}">
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('forum.forums') }}
    </li>
@endsection

@section('nav-tabs')
    @include('forum.partials.buttons')
@endsection

@section('page', 'page__forums--index')

@section('main')
    @foreach ($categories as $category)
        <section class="panelV2">
            <h2 class="panel__heading">
                <a class="panel__header-link" href="{{ route('forums.categories.show', ['id' => $category->id]) }}">
                    {{ $category->name }}
                </a>
            </h2>
            @if ($category->forums->count() > 0)
                <ul class="subforum-listings">
                    @foreach ($category->forums->sortBy('position') as $forum)
                        <li class="subforum-listings__item">
                            <x-forum.subforum-listing :subforum="$forum" />
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="panel__body">
                    No forums in category.
                </div>
            @endif
        </section>
    @endforeach
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('forum.stats') }}</h2>
        <dl class="key-value">
            <dt>{{ __('forum.forums') }}</dt>
            <dd>{{ $num_forums }}</dd>
            <dt>{{ __('forum.topics') }}</dt>
            <dd>{{ $num_topics }}</dd>
            <dt>{{ __('forum.posts') }}</dt>
            <dd>{{ $num_posts }}</dd>
        </dl>
    </section>
@endsection
