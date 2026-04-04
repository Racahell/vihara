@extends('layouts.app')
@section('title', 'Detail Kegiatan')
@section('content')
<div class="card">
    <h2>{{ $activity->title }}</h2>
    <div class="muted">{{ $activity->start_at->format('d M Y H:i') }} | {{ $activity->location }}</div>
    <p>{{ $activity->description }}</p>
    <p>Kuota: {{ $activity->registered_count }} / {{ $activity->quota }}</p>
    <div class="muted" style="margin-bottom:8px;">
        Kontak akun pendaftar: {{ auth()->user()->email }} / {{ auth()->user()->phone ?: '-' }}
    </div>
    <form method="POST" action="{{ route('umat.activities.register', $activity) }}" class="form-grid" id="register-multi-form">
        @csrf
        <div id="participants-wrapper" style="display:grid;gap:10px;">
            @php
                $defaultParticipants = !empty($savedParticipants ?? [])
                    ? $savedParticipants
                    : [['name' => auth()->user()->name, 'age' => '', 'gender' => '', 'address' => '']];
                $oldParticipants = old('participants', $defaultParticipants);
            @endphp
            @foreach($oldParticipants as $index => $participant)
                <div class="card participant-item" style="padding:12px;display:grid;gap:8px;" data-participant-item>
                    <div class="muted" style="margin-bottom:8px;">Peserta {{ $index + 1 }}</div>
                    <input type="text" name="participants[{{ $index }}][name]" value="{{ $participant['name'] ?? '' }}" placeholder="Nama peserta" required>
                    <input type="number" name="participants[{{ $index }}][age]" value="{{ $participant['age'] ?? '' }}" min="0" max="120" placeholder="Usia" required>
                    <select name="participants[{{ $index }}][gender]" required>
                        <option value="">Pilih jenis kelamin</option>
                        <option value="L" @selected(($participant['gender'] ?? '') === 'L')>Laki-laki</option>
                        <option value="P" @selected(($participant['gender'] ?? '') === 'P')>Perempuan</option>
                    </select>
                    <input type="text" name="participants[{{ $index }}][address]" value="{{ $participant['address'] ?? '' }}" placeholder="Alamat peserta" required>
                    <div>
                        <button type="button" class="btn btn-secondary" data-remove-participant>Hapus Peserta</button>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
            <button type="button" class="btn btn-secondary" id="add-participant-btn">Tambah Peserta</button>
            <button type="submit">Daftar Kegiatan</button>
        </div>
    </form>
</div>

<template id="participant-template">
    <div class="card participant-item" style="padding:12px;display:grid;gap:8px;" data-participant-item>
        <div class="muted" style="margin-bottom:8px;" data-participant-title>Peserta</div>
        <input type="text" data-field="name" placeholder="Nama peserta" required>
        <input type="number" data-field="age" min="0" max="120" placeholder="Usia" required>
        <select data-field="gender" required>
            <option value="">Pilih jenis kelamin</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
        </select>
        <input type="text" data-field="address" placeholder="Alamat peserta" required>
        <div>
            <button type="button" class="btn btn-secondary" data-remove-participant>Hapus Peserta</button>
        </div>
    </div>
</template>

<script>
    (function () {
        const wrapper = document.getElementById('participants-wrapper');
        const template = document.getElementById('participant-template');
        const addBtn = document.getElementById('add-participant-btn');

        if (!wrapper || !template || !addBtn) return;

        const refreshIndexes = () => {
            const items = wrapper.querySelectorAll('[data-participant-item]');
            items.forEach((item, index) => {
                const title = item.querySelector('[data-participant-title]');
                if (title) title.textContent = `Peserta ${index + 1}`;

                const nameInput = item.querySelector('input[data-field="name"], input[name*="[name]"]');
                const ageInput = item.querySelector('input[data-field="age"], input[name*="[age]"]');
                const genderSelect = item.querySelector('select[data-field="gender"], select[name*="[gender]"]');
                const addressInput = item.querySelector('input[data-field="address"], input[name*="[address]"]');

                if (nameInput) nameInput.name = `participants[${index}][name]`;
                if (ageInput) ageInput.name = `participants[${index}][age]`;
                if (genderSelect) genderSelect.name = `participants[${index}][gender]`;
                if (addressInput) addressInput.name = `participants[${index}][address]`;

                const removeBtn = item.querySelector('[data-remove-participant]');
                if (removeBtn) {
                    removeBtn.disabled = items.length <= 1;
                }
            });
        };

        addBtn.addEventListener('click', () => {
            const node = template.content.firstElementChild.cloneNode(true);
            wrapper.appendChild(node);
            refreshIndexes();
        });

        wrapper.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;
            if (!target.matches('[data-remove-participant]')) return;

            const item = target.closest('[data-participant-item]');
            if (!item) return;
            item.remove();
            refreshIndexes();
        });

        refreshIndexes();
    })();
</script>
@endsection
