@php
$data = $jsapp['page']['schema']
@endphp
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": {{ $data['rating'] }},
    "reviewCount": {{ $data['reviews_count'] }},
  },
  "description": {{ $data['description'] }},
  "name": {{ $data['name'] }},
  "image": {{ $data['image'] }},
  "offers": {
    "@type": "Offer",
    "availability": "https://schema.org/InStock",
    "price": {{ $data['price'] }},
    "priceCurrency": "IDR"
  }
}
</script>
