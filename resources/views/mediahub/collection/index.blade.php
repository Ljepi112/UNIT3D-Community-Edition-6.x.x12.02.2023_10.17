@extends('layout.default')

@section('title')
    <title>{{ __('mediahub.collections') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('mediahub.collections') }}">
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('mediahub.collections') }}
    </li>
@endsection

@section('content')
    @livewire('collection-search')
@endsection
