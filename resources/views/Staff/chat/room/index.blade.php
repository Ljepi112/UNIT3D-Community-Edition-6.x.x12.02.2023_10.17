@extends('layout.default')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.chat-rooms') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('staff.statuses.index') }}">
            {{ __('staff.statuses') }}
        </a>
    </li>
    <li class="nav-tab--active">
        <a class="nav-tab--active__link" href="{{ route('staff.rooms.index') }}">
            {{ __('staff.rooms') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('staff.bots.index') }}">
            {{ __('staff.bots') }}
        </a>
    </li>
@endsection

@section('page', 'page__chat-room--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('common.chat-rooms') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.rooms.create') }}"
                    >
                        {{ __('common.add') }}
                    </a>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('common.name') }}</th>
                    <th>{{ __('common.action') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($chatrooms as $chatroom)
                    <tr>
                        <td>{{ $chatroom->id }}</td>
                        <td>
                            <a href="{{ route('staff.rooms.edit', ['id' => $chatroom->id]) }}">
                                {{ $chatroom->name }}
                            </a>
                        </td>
                        <td>
                            <menu class="data-table__actions">
                                <li class="data-table__action">
                                    <a
                                        class="form__button form__button--text"
                                        href="{{ route('staff.rooms.edit', ['id' => $chatroom->id]) }}"
                                    >
                                        {{ __('common.edit') }}
                                    </a>
                                </li>
                                <li class="data-table__action">
                                    <form
                                        method="POST"
                                        action="{{ route('staff.rooms.destroy', ['id' => $chatroom->id]) }}"
                                        x-data
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            x-on:click.prevent="Swal.fire({
                                                title: 'Are you sure?',
                                                text: 'Are you sure you want to delete this chatroom: {{ $chatroom->name }}?',
                                                icon: 'warning',
                                                showConfirmButton: true,
                                                showCancelButton: true,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $root.submit();
                                                }
                                            })"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.delete') }}
                                        </button>
                                    </form>
                                </li>
                            </menu>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
