@extends('layouts.app')
@section('title', 'Backup Restore')
@section('content')
<div class="card page-head">
    <h2>Backup &amp; Restore Data</h2>
    <p class="muted">Backup database Vihara dalam format SQL, bisa semua tabel atau per tabel.</p>
</div>

<div class="card" style="margin-top:14px;">
    <div class="tabs">
        <a href="{{ route('admin.backup-restore.index', ['tab' => 'backup']) }}" class="tab {{ $tab === 'backup' ? 'active' : '' }}">Backup Restore</a>
        <a href="{{ route('admin.backup-restore.index', ['tab' => 'restore']) }}" class="tab {{ $tab === 'restore' ? 'active' : '' }}">Restore Data</a>
        <a href="{{ route('admin.backup-restore.index', ['tab' => 'clear']) }}" class="tab {{ $tab === 'clear' ? 'active' : '' }}">Hapus Data</a>
    </div>
</div>

@if($tab === 'backup')
    <div class="card" style="margin-top:12px;">
        <h3>Backup Data</h3>
        <p class="muted">Total tabel yang dibackup: <strong>{{ $tableCount }}</strong> tabel (tidak termasuk tabel sistem sementara).</p>
        <form action="{{ route('admin.backup-restore.backup') }}" method="POST" class="form-grid" style="margin-top:12px;">
            @csrf
            <div>
                <label for="selected_table_backup">Pilih Tabel (Opsional)</label>
                <select id="selected_table_backup" name="selected_table">
                    <option value="">Semua tabel</option>
                    @foreach($tables as $table)
                        <option value="{{ $table }}">{{ $table }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Download Backup Database (.sql)</button>
        </form>
    </div>
@endif

@if($tab === 'restore')
    <div class="card" style="margin-top:12px;">
        <h3>Restore Data</h3>
        <p class="muted">Upload file backup `.sql`. Bisa restore semua tabel atau hanya 1 tabel yang dipilih.</p>
        <form action="{{ route('admin.backup-restore.restore') }}" method="POST" enctype="multipart/form-data" class="form-grid" style="margin-top:12px;">
            @csrf
            <div>
                <label for="selected_table_restore">Pilih Tabel (Opsional)</label>
                <select id="selected_table_restore" name="selected_table">
                    <option value="">Semua tabel dari file</option>
                    @foreach($tables as $table)
                        <option value="{{ $table }}">{{ $table }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="backup_file">File Backup</label>
                <input id="backup_file" type="file" name="backup_file" accept=".sql,.txt" required>
            </div>
            <button class="btn btn-danger" type="submit" onclick="return confirm('Lanjut restore data? Data saat ini akan diganti.');">
                Restore Sekarang
            </button>
        </form>
    </div>
@endif

@if($tab === 'clear')
    <div class="card" style="margin-top:12px;">
        <h3>Hapus Semua Data (Kecuali Superadmin)</h3>
        <p class="muted">Aksi ini akan mengosongkan data operasional dari semua tabel aplikasi, tetapi user dengan role superadmin tetap dipertahankan.</p>
        <form action="{{ route('admin.backup-restore.clear-data') }}" method="POST" style="margin-top:12px;">
            @csrf
            <button class="btn btn-danger" type="submit" onclick="return confirm('Yakin hapus semua data? Akun superadmin akan tetap dipertahankan.');">
                Hapus Semua Data Sekarang
            </button>
        </form>
    </div>

    <div class="card" style="margin-top:12px;">
        <h3>Hapus 1 Tabel</h3>
        <p class="muted">Aksi ini mengosongkan isi 1 tabel yang dipilih. Struktur tabel tetap ada.</p>
        <form action="{{ route('admin.backup-restore.clear-table') }}" method="POST" class="form-grid" style="margin-top:12px;">
            @csrf
            <div>
                <label for="selected_table_clear">Pilih Tabel</label>
                <select id="selected_table_clear" name="selected_table" required>
                    <option value="">-- Pilih tabel --</option>
                    @foreach($tables as $table)
                        <option value="{{ $table }}">{{ $table }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-danger" type="submit" onclick="return confirm('Yakin hapus semua data dari tabel terpilih?');">
                Hapus Tabel Terpilih
            </button>
        </form>
    </div>
@endif
@endsection
