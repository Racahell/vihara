@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('content')
<div class="table-wrap">
<table>
    <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Deskripsi</th><th>Target</th></tr></thead>
    <tbody>
    @foreach($logs as $log)
        <tr>
            <td>{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
            <td>{{ $log->user_id ?? '-' }}</td>
            <td>{{ $log->action }}</td>
            <td>{{ $log->description }}</td>
            <td>{{ $log->target_type }}#{{ $log->target_id }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
{{ $logs->links() }}
@endsection
