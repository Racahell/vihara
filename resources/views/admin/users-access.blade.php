@extends('layouts.app')
@section('title', 'Hak Akses')
@section('content')
<div class="card page-head">
    <h2>Hak Akses</h2>
    <p class="muted">
        Superadmin memiliki seluruh hak akses secara penuh dan tidak dapat diubah.
        Hak akses hanya dapat diatur untuk role selain Superadmin.
    </p>
</div>

<div class="card" style="margin-top:16px;">
    <form method="GET" class="table-toolbar" style="margin:0;">
        <div class="table-length">
            <label for="role_id">Pilih Role</label>
            <select id="role_id" name="role_id" onchange="this.form.submit()">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected(($selectedRole?->id ?? 0) === $role->id)>
                        {{ $role->name }} ({{ $role->users_count }} user)
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

@if($selectedRole)
    <form method="POST" action="{{ route('admin.users.access.update') }}" data-access-matrix style="margin-top:12px;">
        @csrf
        <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">

        <div class="table-wrap">
            <table class="users-table">
                <thead>
                <tr>
                    <th style="width:38%;">Nama Menu</th>
                    <th style="width:12%;" class="access-checkbox-cell">View</th>
                    <th style="width:12%;" class="access-checkbox-cell">Create</th>
                    <th style="width:12%;" class="access-checkbox-cell">Edit</th>
                    <th style="width:12%;" class="access-checkbox-cell">Delete</th>
                    <th style="width:14%;" class="access-checkbox-cell">Select All</th>
                </tr>
                </thead>
                <tbody>
                @foreach($matrix as $row)
                    @php
                        $hasView = in_array('view', $row['actions'], true);
                        $hasCreate = in_array('create', $row['actions'], true);
                        $hasEdit = in_array('edit', $row['actions'], true);
                        $hasDelete = in_array('delete', $row['actions'], true);
                        $viewSlug = $row['key'] . '.view';
                        $createSlug = $row['key'] . '.create';
                        $editSlug = $row['key'] . '.edit';
                        $deleteSlug = $row['key'] . '.delete';
                    @endphp
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td class="access-checkbox-cell">
                            @if($hasView)
                                <input class="access-checkbox" type="checkbox" name="permissions[]" value="{{ $viewSlug }}"
                                    data-access-row="{{ $row['key'] }}" data-access-item
                                    @checked(in_array($viewSlug, $selectedPermissionSlugs, true))
                                    @disabled($isSuperadminRole)>
                            @else
                                -
                            @endif
                        </td>
                        <td class="access-checkbox-cell">
                            @if($hasCreate)
                                <input class="access-checkbox" type="checkbox" name="permissions[]" value="{{ $createSlug }}"
                                    data-access-row="{{ $row['key'] }}" data-access-item
                                    @checked(in_array($createSlug, $selectedPermissionSlugs, true))
                                    @disabled($isSuperadminRole)>
                            @else
                                -
                            @endif
                        </td>
                        <td class="access-checkbox-cell">
                            @if($hasEdit)
                                <input class="access-checkbox" type="checkbox" name="permissions[]" value="{{ $editSlug }}"
                                    data-access-row="{{ $row['key'] }}" data-access-item
                                    @checked(in_array($editSlug, $selectedPermissionSlugs, true))
                                    @disabled($isSuperadminRole)>
                            @else
                                -
                            @endif
                        </td>
                        <td class="access-checkbox-cell">
                            @if($hasDelete)
                                <input class="access-checkbox" type="checkbox" name="permissions[]" value="{{ $deleteSlug }}"
                                    data-access-row="{{ $row['key'] }}" data-access-item
                                    @checked(in_array($deleteSlug, $selectedPermissionSlugs, true))
                                    @disabled($isSuperadminRole)>
                            @else
                                -
                            @endif
                        </td>
                        <td class="access-checkbox-cell">
                            <input class="access-checkbox" type="checkbox" data-access-row-toggle="{{ $row['key'] }}"
                                @disabled($isSuperadminRole)>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="card" style="margin-top:12px;">
            @if($isSuperadminRole)
                <p class="muted" style="margin:0;">Hak akses Superadmin bersifat penuh dan dikunci (readonly).</p>
            @endif
            <div class="modal-footer-actions access-footer-actions">
                <button class="btn btn-secondary" type="button" onclick="window.location='{{ route('admin.users.access', ['role_id' => $selectedRole->id]) }}'">Reset</button>
                <button class="btn btn-primary" type="submit" @disabled($isSuperadminRole)>Simpan</button>
            </div>
        </div>
    </form>
@endif
@endsection
