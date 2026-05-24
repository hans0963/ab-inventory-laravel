<div class="card-rustic border-sienna">
    <h3 class="text-xl font-lora font-bold text-sienna mb-4">{{ $title }}</h3>
    <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                    @foreach($columns as $label)
                        <th class="px-4 py-3 text-left">{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                @forelse($rows as $row)
                    <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                        @foreach($columns as $field => $label)
                            @php $value = data_get($row, $field); @endphp
                            <td class="px-4 py-3 {{ is_numeric($value) ? 'font-bold text-terracotta' : 'text-sienna' }}">
                                @if(str_contains($field, 'revenue') || str_contains($field, 'total'))
                                    PHP {{ number_format((float) $value, 2) }}
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-4 py-8 text-center text-gray-500 italic">No data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
