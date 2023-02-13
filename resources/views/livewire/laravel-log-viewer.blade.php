@section('title')
    <title>Laravel Log Viewer - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Laravel Log Viewer - {{ __('staff.staff-dashboard') }}">
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        Laravel Log Viewer
    </li>
@endsection

<div style="display: grid; grid-template-columns: 1fr max-content; gap: 12px; align-items: flex-start">
    <section class="panelV2">
        <h2 class="panel__heading">
            <i class="{{ config('other.font-awesome') }} fa-list"></i>
            Laravel Log Viewer
        </h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Level</th>
                        <th>Message</th>
                        <th>Exception</th>
                        <th>In</th>
                        <th>Line</th>
                        <th>Count</th>
                    </tr>
                </thead>
                @forelse($entries as $message => $groupedEntry)
                    <tbody x-data="{ expanded: false }" style="border-top: 0;">
                        <tr x-on:click="expanded = !expanded" style="cursor: pointer;">
                            <td>{{ $groupedEntry[0]['date'] }}</td>
                            <td>
                                @switch ($groupedEntry[0]['level'])
                                    @case ('CRITICAL')
                                        <span class="text-danger">{{ $groupedEntry[0]['level'] }}</span>
                                        @break
                                    @case ('ERROR')
                                        <span class="text-warning">{{ $groupedEntry[0]['level'] }}</span>
                                        @break
                                    @case ('INFO')
                                        <span class="text-info">{{ $groupedEntry[0]['level'] }}</span>
                                        @break
                                    @case ('WARNING')
                                        <span class="text-info">{{ $groupedEntry[0]['level'] }}</span>
                                        @break
                                    @default
                                        {{ $groupedEntry[0]['level'] }}
                                @endswitch
                            </td>
                            <td>{{ $groupedEntry[0]['message'] }}</td>
                            <td>{{ $groupedEntry[0]['exception'] }}</td>
                            <td>{{ $groupedEntry[0]['in'] }}</td>
                            <td>{{ $groupedEntry[0]['line'] }}</td>
                            <td>{{ count($groupedEntry) }}</td>
                        </tr>
                        <tr x-cloak x-show="expanded">
                            <td colspan="7" style="padding: 0 0 0 8px;">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Environment</th>
                                            <th>Stacktrace</th>
                                        </tr>
                                    </thead>
                                    @foreach($groupedEntry as $entry)
                                        <tbody x-data="{ expanded: false }" style="border-top: 0;">
                                            <tr x-on:click="expanded = !expanded" style="cursor: pointer;">
                                                <td>{{ $entry['date'] }}</td>
                                                <td>{{ $entry['env'] }}</td>
                                                <td>
                                                    <button
                                                        class="form__button form__button--text"
                                                        x-on:click.stop="navigator.clipboard.writeText($refs.stacktrace.textContent)"
                                                    >
                                                        Copy
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr x-cloack x-show="expanded">
                                                <td colspan="2">
                                                    <code><pre x-ref="stacktrace">{{ $entry['stacktrace'] }}</pre></code>
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="8">No logs have been created yet.</td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>
        {{ $entries->links('partials.pagination') }}
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">Entries</h2>
        <select multiple wire:model="logs" style="height: 320px; padding: 8px; border-radius: 4px; width: 100%">
            @foreach($files as $file)
                <option value="{{ $loop->index }}" style="padding: 6px; border-radius: 4px; cursor: pointer;">
                    {{ $file->getFilename() }}
                </option>
            @endforeach
        </select>
    </section>
</div>