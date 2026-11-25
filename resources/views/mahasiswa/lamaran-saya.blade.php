@extends('mahasiswa.layout')

@section('title', 'Lamaran Saya')

@section('content')
    <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-6">Lamaran Saya</h2>

    @php
        $status = [
            null => 'Semua',
            'pending' => 'pending',
            'accepted' => 'accepted',
            'rejected' => 'rejected',
        ];
    @endphp

    <div class="flex items-center gap-2 mb-8 flex-wrap">
        @foreach ($status as $key => $label)
            @php
                $isActive = ($currentStatus === $key);
                $url = $key
                    ? route('mahasiswa.lamaran-saya', ['status' => $key])
                    : route('mahasiswa.lamaran-saya');
            @endphp
            <a href="{{ $url }}"
                class="px-4 py-1.5 rounded-full text-xs transition {{ $isActive ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-medium' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 text-xs font-semibold tracking-wider">
                        <th class="py-4 px-6">Proyek</th>
                        <th class="py-4 px-6">Owner</th>
                        <th class="py-4 px-6">Tanggal</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($applications ?? collect() as $application)
                        @php
                            $statusVal = $application->status ?? 'pending';
                            $badgeClass = match ($statusVal) {
                                'accepted' => 'bg-indigo-50 text-indigo-700 font-semibold',
                                'rejected' => 'bg-indigo-50/40 text-indigo-400 font-medium',
                                default => 'bg-indigo-50/60 text-indigo-600 font-medium',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                {{ $application->project?->name ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $application->project?->owner?->name ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $application->created_at?->translatedFormat('d M Y') ?? '-' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-block px-3 py-1 text-xs rounded-full {{ $badgeClass }}">
                                    {{ $statusVal }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if ($statusVal === 'accepted')
                                    <a href="{{ route('mahasiswa.tim-saya') }}"
                                        class="inline-block py-2 px-4 btn-indigo-custom text-white font-medium rounded-xl text-xs transition shadow-sm">
                                        Buka tim
                                    </a>
                                @else
                                    <button type="button"
                                        data-project="{{ $application->project?->name ?? '-' }}"
                                        data-owner="{{ $application->project?->owner?->name ?? '-' }}"
                                        data-date="{{ $application->created_at?->translatedFormat('d M Y') ?? '-' }}"
                                        data-status="{{ $statusVal }}"
                                        data-motivation="{{ $application->message ?? '-' }}"
                                        onclick="openDetailModal(this)"
                                        class="py-2 px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium rounded-xl text-xs transition">
                                        Lihat
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 px-6 text-center text-sm text-slate-500">
                                Belum ada lamaran{{ $currentStatus ? " dengan status {$currentStatus}" : '' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="detailModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full p-6 lg:p-8">
            <h3 id="detailProject" class="text-xl font-bold text-slate-900 mb-1"></h3>
            <p id="detailMeta" class="text-xs text-slate-500 mb-5"></p>

            <div class="mb-4">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</span>
                <span id="detailStatus" class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 font-semibold text-xs rounded-full"></span>
            </div>

            <div class="mb-6">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Pesan Motivasi</span>
                <p id="detailMotivation" class="text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl p-3.5"></p>
            </div>

            <button type="button" onclick="closeDetailModal()"
                class="py-2.5 px-5 bg-white border border-slate-300 text-slate-700 font-medium rounded-xl text-sm hover:bg-slate-50 transition">
                Tutup
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openDetailModal(button) {
        document.getElementById('detailProject').innerText = button.getAttribute('data-project');
        document.getElementById('detailMeta').innerText =
            'Owner: ' + button.getAttribute('data-owner') + ' • Tanggal: ' + button.getAttribute('data-date');
        document.getElementById('detailStatus').innerText = button.getAttribute('data-status');
        document.getElementById('detailMotivation').innerText = button.getAttribute('data-motivation');
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
</script>
@endpush