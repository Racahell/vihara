@extends('layouts.app')
@section('title', 'Kegiatan Umat')
@section('content')
<div class="cards">
    @foreach($activities as $activity)
        <div class="card">
            <h3>{{ $activity->title }}</h3>
            <div class="muted">{{ $activity->start_at->format('d M Y H:i') }} | {{ $activity->location }}</div>
            <p>{{ \Illuminate\Support\Str::limit($activity->description, 120) }}</p>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a class="btn btn-green" href="{{ route('umat.activities.show', $activity) }}">Detail</a>
                <form action="{{ route('umat.activities.favorite', $activity) }}" method="POST">@csrf<button type="submit">Favorit</button></form>
            </div>
            @if(in_array($activity->id, $favoriteIds))
                <div class="pill" style="margin-top:8px;">Tersimpan di Favorit</div>
            @endif
        </div>
    @endforeach
</div>
{{ $activities->links() }}
@endsection
