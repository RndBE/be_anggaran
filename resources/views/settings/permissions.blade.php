<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-foreground">Manajemen Permission</h2>
            <p class="text-sm text-muted-foreground mt-0.5">Kelola akses setiap role ke fitur sistem</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert-success mb-4">{{ session('success') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="card-title">Permission & Role Matrix</h3>
                    <p class="card-description mt-1">Centang permission mana saja yang diberikan ke setiap role.
                        Perubahan langsung disimpan ke database.</p>
                </div>
                <form method="POST" action="{{ route('settings.permissions.update') }}">
                    @csrf
                    <div class="table-wrapper">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/3">Permission</th>
                                    @foreach($roles as $role)
                                        <th class="text-center">{{ $role->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $permission)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-foreground">{{ $permission->name }}</div>
                                            @if($permission->description)
                                                <div class="text-xs text-muted-foreground mt-0.5">{{ $permission->description }}
                                                </div>
                                            @endif
                                            <code
                                                class="text-xs text-primary font-mono bg-primary/10 px-1.5 py-0.5 rounded mt-1 inline-block">{{ $permission->slug }}</code>
                                        </td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="perm_{{ $permission->id }}[]"
                                                    value="{{ $role->id }}"
                                                    class="h-4 w-4 rounded border-input text-primary focus:ring-ring cursor-pointer"
                                                    @if($permission->roles->contains($role->id)) checked @endif>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-border flex items-center justify-between">
                        <p class="text-xs text-muted-foreground">Perubahan berlaku segera untuk semua user dengan role
                            tersebut.</p>
                        <button type="submit" class="btn-default">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>