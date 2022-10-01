@php
$data = $jsapp['page']['product_schema']
@endphp
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "description": "{{ $data['description'] }}",
  "name": "{{ $data['name'] }}",
  "image": "{{ $data['image'] }}",
  "offers": {
    "@type": "Offer",
    "availability": "https://schema.org/InStock",
    "price": {{ $data['price'] }},
    "priceCurrency": "IDR"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{ $data['rating'] }}",
    "reviewCount": "{{ $data['reviews_count'] }}"
  },
  "review": {
    "@type": "Review",
    "reviewRating": {
      "@type": "Rating",
      "ratingValue": "{{ $data['rating'] }}",
      "bestRating": "5"
    }
}
</script>
