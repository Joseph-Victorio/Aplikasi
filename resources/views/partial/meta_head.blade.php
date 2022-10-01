@if($jsapp['page']['title'])
<title>{{ $jsapp['page']['title'] }}</title>
<meta property="og:title" content="{{ $jsapp['page']['title'] }}" data-qmeta="ogTitle">
<meta name="twitter:title" content="{{ $jsapp['page']['title'] }}" data-qmeta="ogTitle">
@else
<title>{{ $jsapp['shop']['sitename'] }}</title>
<meta property="og:title" content="{{ $jsapp['shop']['sitename'] }}" data-qmeta="ogTitle">
<meta name="twitter:title" content="{{ $jsapp['shop']['sitename'] }}" data-qmeta="ogTitle">
@endif

<meta property="og:url" content="{{ url()->full() }}">
<meta property="og:type" content="website" data-qmeta="ogType"/>
<meta property="og:site_name" content="{{ $jsapp['shop']['sitename'] }}" data-qmeta="ogSitename">

<meta name="twitter:card" content="summary_large_image">
<meta property="twitter:domain" content="{{ $jsapp['shop']['sitename'] }}">
<meta property="twitter:url" content="{{ url()->full() }}">

@if(isset($jsapp['page']['description']) && $jsapp['page']['description'])
<meta name="description" content="{{ $jsapp['page']['description'] }}">
<meta property="og:description" content="{{ $jsapp['page']['description'] }}" data-qmeta="ogDescription">
<meta name="twitter:description" content="{{ $jsapp['page']['description'] }}" data-qmeta="ogDescription">
@else
<meta name="description" content="{{ $jsapp['shop']['description'] }}">
<meta property="og:description" content="{{ $jsapp['shop']['description'] }}" data-qmeta="ogDescription">
<meta name="twitter:description" content="{{ $jsapp['shop']['description'] }}" data-qmeta="ogDescription">
@endif

@if(isset($jsapp['page']['featured_image']) && $jsapp['page']['featured_image'])
<meta property="og:image" content="{{ $jsapp['page']['featured_image'] }}" data-qmeta="ogImage">
<meta name="twitter:image" content="{{ $jsapp['page']['featured_image'] }}" data-qmeta="ogImage">
@endif
