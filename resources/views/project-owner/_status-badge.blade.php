@php
    $colors = [
        'approved' => 'bg-emerald-50 text-emerald-700',
        'pending' => 'bg-amber-50 text-amber-700',
        'rejected' => 'bg-rose-50 text-rose-700',
    ];
@endphp
<span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $colors[$status] ?? 'bg-slate-100 text-slate-600' }}">{{ $status }}</span>
