@extends('layouts.app')
@section('title', ($favoritesOnly ?? false) ? 'Favorit Kegiatan' : 'Kegiatan Umat')
@section('content')
<div class="card page-head">
    <h2>{{ ($favoritesOnly ?? false) ? 'Favorit Kegiatan Saya' : 'Kegiatan Umat' }}</h2>
    <p class="muted">{{ ($favoritesOnly ?? false) ? 'Daftar kegiatan yang sudah kamu tandai sebagai favorit.' : 'Pilih kegiatan aktif yang ingin kamu ikuti.' }}</p>
</div>
<div class="cards">
    @foreach($activities as $activity)
        @php
            $isFavorite = in_array($activity->id, $favoriteIds, true);
        @endphp
        <div class="card">
            <h3>{{ $activity->title }}</h3>
            <div class="muted">{{ $activity->start_at->format('d M Y H:i') }} | {{ $activity->location }}</div>
            <p>{{ \Illuminate\Support\Str::limit($activity->description, 120) }}</p>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a class="btn btn-green" href="{{ route('umat.activities.show', $activity) }}">Detail</a>
                <form action="{{ route('umat.activities.favorite', $activity) }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="btn-favorite {{ $isFavorite ? 'is-active' : '' }}"
                        aria-label="{{ $isFavorite ? 'Hapus dari favorit' : 'Tambah ke favorit' }}"
                        title="{{ $isFavorite ? 'Hapus dari favorit' : 'Tambah ke favorit' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 20.4l-1.2-1.1C6 15 3 12.3 3 8.9 3 6.2 5.2 4 7.9 4c1.6 0 3.1.8 4.1 2 1-1.2 2.5-2 4.1-2C18.8 4 21 6.2 21 8.9c0 3.4-3 6.1-7.8 10.4L12 20.4z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>
{{ $activities->links() }}
@endsection
