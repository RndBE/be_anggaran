<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.users.index') }}" class="text-muted-foreground hover:text-foreground transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-foreground">Edit User</h2>
                <p class="text-sm text-muted-foreground font-medium">{{ $user->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="alert-destructive mb-4">
                    <ul class="list-disc pl-4 space-y-1 text-sm">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="card p-6">
                <form method="POST" action="{{ route('settings.users.update', $user) }}" class="space-y-5" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <x-input-label for="name">Nama Lengkap</x-input-label>
                        <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <x-input-label for="email">Email</x-input-label>
                            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required />
                        </div>
                        <div class="form-group">
                            <x-input-label for="password">Password Baru</x-input-label>
                            <x-text-input id="password" name="password" type="password" placeholder="Kosongkan jika tidak ingin diubah" />
                            <p class="text-xs text-muted-foreground mt-1">Isi hanya jika ingin mengganti password.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <x-input-label for="division_id">Divisi</x-input-label>
                            <select id="division_id" name="division_id" class="select-input">
                                <option value="">— Tidak ada divisi —</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}"
                                        {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <x-input-label for="level">Level Jabatan</x-input-label>
                            <select id="level" name="level" class="select-input">
                                <option value="">— Tidak ada —</option>
                                @foreach([4 => 'Level 4 (Staff/Pengaju)', 3 => 'Level 3 (Supervisor)', 2 => 'Level 2 (Manager)', 1 => 'Level 1 (Director)'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('level', $user->level) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <x-input-label>Tanda Tangan</x-input-label>
                        @if($user->signature)
                            <div class="mt-2 mb-3 p-3 bg-muted/30 rounded-lg border border-border inline-flex flex-col items-center gap-2">
                                <img src="{{ asset('storage/' . $user->signature) }}" alt="Tanda tangan {{ $user->name }}" class="h-16 object-contain">
                                <span class="text-xs text-muted-foreground">Tanda tangan saat ini</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-3 border-2 border-dashed border-border rounded-lg p-3 bg-muted/20 hover:bg-muted/40 transition-colors cursor-pointer"
                            onclick="document.getElementById('signature').click()">
                            <svg class="w-6 h-6 text-muted-foreground shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            <span class="text-sm text-muted-foreground" id="sig_label">
                                {{ $user->signature ? 'Ganti tanda tangan…' : 'Pilih file tanda tangan…' }}
                            </span>
                        </div>
                        <input type="file" id="signature" name="signature" accept=".jpg,.jpeg,.png,.gif,.svg" class="hidden"
                            onchange="document.getElementById('sig_label').textContent = this.files[0]?.name ?? '...'">
                        <p class="text-xs text-muted-foreground mt-1">JPG, PNG, SVG · Maks 2 MB.</p>
                        @error('signature')
                            <x-input-error :messages="[$message]" />
                        @enderror
                    </div>

                    <div class="form-group">
                        <x-input-label>Assign Roles</x-input-label>
                        <div class="mt-2 space-y-2">
                            @foreach($roles as $role)
                                @php
                                    $checked = old('roles')
                                        ? in_array($role->id, old('roles', []))
                                        : $user->roles->contains($role->id);
                                @endphp
                                <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors
                                    {{ $checked ? 'bg-primary/5 border-primary/30' : 'border-border hover:bg-accent' }}">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        {{ $checked ? 'checked' : '' }}
                                        class="rounded border-input text-primary focus:ring-ring">
                                    <div>
                                        <span class="text-sm font-medium text-foreground">{{ $role->name }}</span>
                                        <code class="ml-2 text-xs text-primary font-mono bg-primary/10 px-1 rounded">{{ $role->slug }}</code>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-border flex justify-between">
                        <a href="{{ route('settings.users.index') }}" class="btn-outline">Batal</a>
                        <button type="submit" class="btn-default">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
