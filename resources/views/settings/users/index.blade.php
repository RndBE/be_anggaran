<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-foreground">Manajemen User</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Kelola akun pengguna dalam sistem</p>
            </div>
            <a href="{{ route('settings.users.create') }}" class="btn-default btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="alert-success flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->has('error'))
                <div class="alert-destructive">{{ $errors->first('error') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="w-10">#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Divisi / Level</th>
                                <th>Roles</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr class="{{ $user->id === Auth::id() ? 'bg-primary/5' : '' }}">
                                    <td class="text-muted-foreground">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <div
                                                class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                                <span
                                                    class="text-xs font-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-foreground text-sm">{{ $user->name }}</p>
                                                @if($user->id === Auth::id())
                                                    <span class="badge-info text-[10px]">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted-foreground text-sm">{{ $user->email }}</td>
                                    <td>
                                        <p class="text-sm text-foreground">{{ $user->division?->name ?? '—' }}</p>
                                        @if($user->level)
                                            <span class="badge-secondary text-[10px]">Level {{ $user->level }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($user->roles as $role)
                                                <span class="badge-warning text-[10px]">{{ $role->name }}</span>
                                            @empty
                                                <span class="text-xs text-muted-foreground italic">Tidak ada role</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('settings.users.edit', $user) }}"
                                                class="btn-outline btn-sm">Edit</a>
                                            @if($user->id !== Auth::id())
                                                <form method="POST" action="{{ route('settings.users.destroy', $user) }}"
                                                    onsubmit="return confirm('Hapus user \'{{ $user->name }}\'?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-destructive btn-sm">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-sm text-muted-foreground">Belum ada user.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>