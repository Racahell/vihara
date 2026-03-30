@extends('layouts.app')
@section('title', 'Detail Kegiatan')
@section('content')
<div class="card">
    <h2>{{ $activity->title }}</h2>
    <div class="muted">{{ $activity->start_at->format('d M Y H:i') }} | {{ $activity->location }}</div>
    <p>{{ $activity->description }}</p>
    <p>Kuota: {{ $activity->registered_count }} / {{ $activity->quota }}</p>
    <form method="POST" action="{{ route('umat.activities.register', $activity) }}">
        @csrf
        <button type="submit">Daftar Kegiatan</button>
    </form>
</div>
@endsection
