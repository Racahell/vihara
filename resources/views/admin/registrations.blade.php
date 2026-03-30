@extends('layouts.app')
@section('title', 'Pendaftaran Kegiatan')
@section('content')
<div class="table-wrap">
<table>
    <thead><tr><th>Kode</th><th>Peserta</th><th>Kegiatan</th><th>Tipe</th><th>Status Hadir</th><th>Waktu Daftar</th></tr></thead>
    <tbody>
    @foreach($registrations as $reg)
        <tr>
            <td>{{ $reg->registration_code }}</td>
            <td>{{ $reg->participant_name }}</td>
            <td>{{ $reg->activity->title ?? '-' }}</td>
            <td>{{ strtoupper($reg->registration_type) }}</td>
            <td>{{ strtoupper($reg->attendance_status) }}</td>
            <td>{{ $reg->registered_at?->format('d-m-Y H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
{{ $registrations->links() }}
@endsection
