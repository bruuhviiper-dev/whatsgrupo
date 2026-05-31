@props(['group'])

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "{{ htmlspecialchars($group->name) }}",
  "description": "{{ htmlspecialchars(Str::limit(strip_tags($group->description), 150)) }}",
  "url": "{{ route('group.show', $group->slug) }}",
  "image": "{{ $group->image_path ? asset('storage/' . $group->image_path) : asset('images/placeholder-group.webp') }}",
  "datePublished": "{{ $group->created_at->toIso8601String() }}",
  "dateModified": "{{ $group->updated_at->toIso8601String() }}"
}
</script>
@endpush
