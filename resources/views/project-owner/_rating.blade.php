@if ($rating)
    <span class="text-xs font-semibold text-amber-600">&#9733; {{ number_format($rating->avg, 1) }}</span>
    <span class="text-xs text-slate-500">({{ $rating->n }} ulasan)</span>
@else
    <span class="text-xs text-slate-400">Belum ada ulasan</span>
@endif
