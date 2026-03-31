@extends('layouts.app')
@section('title', 'Data Kehadiran')
@section('content')
<div class="card page-head">
    <h2>Data Kehadiran</h2>
    <p class="muted">Pantau log check-in berdasarkan tanggal dan kegiatan.</p>
</div>

<div class="card checkin-card checkin-log-card" style="margin-top:14px;">
    <form method="GET" class="checkin-filter-form">
        <div class="checkin-filter-field">
            <label for="log_date">Tanggal Kehadiran</label>
            <input id="log_date" type="date" name="log_date" value="{{ $selectedLogDate ?? now()->toDateString() }}">
        </div>
        <div class="checkin-filter-field">
            <label for="log_activity_id">Kegiatan</label>
            <select id="log_activity_id" name="log_activity_id">
                <option value="">Semua kegiatan</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}" @selected((string) ($selectedLogActivityId ?? '') === (string) $activity->id)>{{ $activity->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="checkin-filter-action">
            <button class="btn btn-secondary" type="submit">Filter Kehadiran</button>
        </div>
    </form>

    <div class="table-wrap checkin-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>ID Registrasi</th>
                    <th>Peserta</th>
                    <th>Kegiatan</th>
                    <th>Metode</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayLogs as $log)
                    <tr>
                        <td>{{ $log->checked_in_at?->format('d-m-Y H:i') }}</td>
                        <td>{{ $log->registration?->registration_code ?? ('#' . $log->activity_registration_id) }}</td>
                        <td>{{ $log->registration?->participant_name ?? '-' }}</td>
                        <td>{{ $log->activity?->title ?? ('#' . $log->activity_id) }}</td>
                        <td>{{ strtoupper($log->method) }}</td>
                        <td>{{ $log->handler?->name ?? ('#' . ($log->handled_by ?? '-')) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="checkin-empty-state">Belum ada log check-in pada filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $todayLogs->links() }}
@endsection
