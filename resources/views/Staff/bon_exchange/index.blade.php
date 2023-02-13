@extends('layout.default')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('bon.bon') }} {{ __('bon.exchange') }}
    </li>
@endsection

@section('content')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('bon.bon') }} {{ __('bon.exchange') }}</h2>
            <div class="panel__actions">
                <a href="{{ route('staff.bon_exchanges.create') }}" class="panel__action">
                    {{ __('common.add') }}
                    {{ trans_choice('common.a-an-art',true) }}
                    {{ __('bon.exchange') }}
                </a>
            </div>
        </header>
        <div class="table-responsive">
            <table class="table table-condensed table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>{{ __('common.name') }}</th>
                    <th>{{ __('value') }}</th>
                    <th>{{ __('bon.points') }}</th>
                    <th>{{ __('common.type') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($bonExchanges as $bonExchange)
                    <tr>
                        <td>{{ $bonExchange->description }}</td>
                        <td>{{ $bonExchange->value }}</td>
                        <td>{{ $bonExchange->cost }}</td>
                        <td>
                            @switch (1)
                                @case ($bonExchange->upload)
                                    {{ __('common.add') }} {{ __('common.upload') }}
                                    @break
                                @case ($bonExchange->download)
                                    {{ __('common.remove') }} {{ __('common.download') }}
                                    @break
                                @case ($bonExchange->personal_freeleech)
                                    {{ __('torrent.personal-freeleech') }}
                                    @break
                                @case ($bonExchange->invite)
                                    {{ __('user.invites') }}
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <form x-data action="{{ route('staff.bon_exchanges.destroy', ['bonExchange' => $bonExchange->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a href="{{ route('staff.bon_exchanges.edit', ['bonExchange' => $bonExchange->id]) }}"
                                   class="form__button form__button--filled">
                                    {{ __('common.edit') }}
                                </a>
                                <button
                                    x-on:click.prevent="Swal.fire({
                                        title: 'Delete?',
                                        text: 'Are you sure you want to delete?',
                                        icon: 'warning',
                                        showConfirmButton: true,
                                        showCancelButton: true,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $root.submit();
                                        }
                                    })"
                                    class="form__button form__button--filled"
                                >
                                    {{ __('common.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
