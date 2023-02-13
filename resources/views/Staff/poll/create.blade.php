@extends('layout.default')

@section('title')
    <title>Polls - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.polls.index') }}" class="breadcrumb__link">
            {{ __('poll.polls') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__poll-admin--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('poll.create-poll') }}</h2>
        <div class="panel-body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.polls.store') }}"
                x-data="{ options: 2 }"
            >
                @csrf
                <p class="form__group">
                    <input
                        id="title"
                        class="form__text"
                        minlength="10"
                        name="title"
                        required
                        type="text"
                        value="{{ old('title') }}"
                    >
                    <label class="form__label form__label--floating" for="title">
                        {{ __('common.title') }}
                    </label>
                </p>
                <template x-for="option in options">
                    <p class="form__group">
                        <input
                            x-bind:id="'option' + option"
                            class="form__text"
                            name="options[]"
                            type="text"
                            placeholder=""
                        >
                        <label class="form__label form__label--floating" x-bind:for="'option' + option">
                            {{ __('poll.option') }}
                        </label>
                    </p>
                </template>
                <p class="form__group">
                    <button
                        x-on:click.prevent="options++"
                        class="form__button form__button--outlined"
                    >
                        {{ __('poll.add-option') }}
                    </button>
                    <button
                        class="form__button form__button--outlined"
                        x-on:click.prevent="options = Math.max(2, options - 1)"
                    >
                        {{ __('poll.delete-option') }}
                    </button>
                </p>
                <p class="form__group">
                    <input
                        id="multiple_choice"
                        class="form__checkbox"
                        name="multiple_choice"
                        type="checkbox"
                        value="1"
                    >
                    <label class="form__label" for="multiple_choice">
                        {{ __('poll.multiple-choice') }}
                    </label>
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('poll.create-poll') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
