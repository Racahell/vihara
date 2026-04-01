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
            <label for="attendance-per-page">Tampilkan</label>
            <select id="attendance-per-page" name="per_page">
                @foreach([10, 25, 50, 100] as $size)
                    <option value="{{ $size }}" @selected((int) ($perPage ?? 10) === $size)>{{ $size }}</option>
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayLogs as $log)
                    @php
                        $registrationCode = $log->registration?->registration_code ?? ('#' . $log->activity_registration_id);
                    @endphp
                    <tr>
                        <td>{{ $log->checked_in_at?->format('d-m-Y H:i') }}</td>
                        <td>{{ $registrationCode }}</td>
                        <td>{{ $log->registration?->participant_name ?? '-' }}</td>
                        <td>{{ $log->activity?->title ?? ('#' . $log->activity_id) }}</td>
                        <td>{{ strtoupper($log->method) }}</td>
                        <td>{{ $log->handler?->name ?? ('#' . ($log->handled_by ?? '-')) }}</td>
                        <td>
                            <button type="button" class="btn btn-outline" data-modal-open="attendance-log-modal-{{ $log->id }}">Detail</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="checkin-empty-state">Belum ada log check-in pada filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($todayLogs as $log)
    @php
        $registrationCode = $log->registration?->registration_code ?? ('#' . $log->activity_registration_id);
        $registrationType = strtoupper((string) ($log->registration?->registration_type ?? '-'));
        $gender = match ($log->registration?->participant_gender) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    @endphp
    <div class="modal" id="attendance-log-modal-{{ $log->id }}" aria-hidden="true">
        <div class="modal-backdrop" data-modal-close="attendance-log-modal-{{ $log->id }}"></div>
        <div class="modal-dialog">
            <div class="modal-header">
                <h3>Detail Kehadiran</h3>
                <button type="button" class="btn btn-secondary" data-modal-close="attendance-log-modal-{{ $log->id }}">Tutup</button>
            </div>
            <div class="modal-body">
                <div class="detail-grid">
                    <div><strong>Waktu Check-in:</strong><br>{{ $log->checked_in_at?->format('d-m-Y H:i') ?? '-' }}</div>
                    <div><strong>Metode:</strong><br>{{ strtoupper((string) $log->method) }}</div>
                    <div><strong>ID Registrasi:</strong><br>{{ $registrationCode }}</div>
                    <div><strong>Jenis Registrasi:</strong><br>{{ $registrationType }}</div>
                    <div><strong>Nama Peserta:</strong><br>{{ $log->registration?->participant_name ?? '-' }}</div>
                    <div><strong>No. Telepon:</strong><br>{{ $log->registration?->participant_phone ?: '-' }}</div>
                    <div><strong>Usia:</strong><br>{{ $log->registration?->participant_age ?? '-' }}</div>
                    <div><strong>Gender:</strong><br>{{ $gender }}</div>
                    <div><strong>Alamat:</strong><br>{{ $log->registration?->participant_address ?: '-' }}</div>
                    <div><strong>Petugas:</strong><br>{{ $log->handler?->name ?? ('#' . ($log->handled_by ?? '-')) }}</div>
                    <div><strong>Kegiatan:</strong><br>{{ $log->activity?->title ?? ('#' . $log->activity_id) }}</div>
                    <div><strong>Lokasi:</strong><br>{{ $log->activity?->location ?: '-' }}</div>
                    <div style="grid-column:1 / -1; display:flex; gap:8px; flex-wrap:wrap;">
                        <button
                            type="button"
                            class="btn btn-outline"
                            data-map-search
                            data-destination="{{ $log->activity?->location ?: '' }}">
                            Lihat di Google Maps
                        </button>
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-map-route
                            data-destination="{{ $log->activity?->location ?: '' }}">
                            Rute dari Lokasi Saya
                        </button>
                    </div>
                    <div><strong>Mulai Kegiatan:</strong><br>{{ $log->activity?->start_at?->format('d-m-Y H:i') ?? '-' }}</div>
                    <div><strong>Selesai Kegiatan:</strong><br>{{ $log->activity?->end_at?->format('d-m-Y H:i') ?? '-' }}</div>
                    <div style="grid-column:1 / -1;"><strong>Catatan:</strong><br>{{ $log->notes ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
@endforeach
{{ $todayLogs->links() }}
@endsection
