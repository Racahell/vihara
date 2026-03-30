@extends('layouts.app')
@section('title', 'Log Login')
@section('content')
<div class="table-wrap">
<table>
    <thead><tr><th>Waktu</th><th>Email</th><th>IP</th><th>Status</th></tr></thead>
    <tbody>
    @foreach($logs as $log)
        <tr>
            <td>{{ $log->logged_in_at?->format('d-m-Y H:i:s') }}</td>
            <td>{{ $log->email }}</td>
            <td>{{ $log->ip_address }}</td>
            <td>{{ $log->successful ? 'Berhasil' : 'Gagal' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
{{ $logs->links() }}
@endsection
