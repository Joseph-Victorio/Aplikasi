<!DOCTYPE html>
<html lang="id" prefix="og: https://ogp.me/ns#">
  <head>
    @if($jsapp['page']['title'])
    <title>{{ $jsapp['page']['title'] }}</title>
    <meta property="og:title" content="{{ $jsapp['page']['title'] }}" data-qmeta="ogTitle">
    @else
    <title>{{ $jsapp['shop']['sitename'] }}</title>
    <meta property="og:title" content="{{ $jsapp['shop']['sitename'] }}" data-qmeta="ogTitle">
    @endif
    
    @if(isset($jsapp['page']['description']) && $jsapp['page']['description'])
    <meta name="description" content="{{ $jsapp['page']['description'] }}">
    <meta property="og:description" content="{{ $jsapp['page']['description'] }}" data-qmeta="ogDescription">
    @else
    <meta name="description" content="{{ $jsapp['shop']['description'] }}">
    <meta property="og:description" content="{{ $jsapp['shop']['description'] }}" data-qmeta="ogDescription">
    @endif

    <meta charset="utf-8">
    <meta name=format-detection content="telephone=no">
    <meta name=msapplication-tap-highlight content=no>
    <meta name=viewport content="user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1,width=device-width">
    <link rel=icon type=image/png href=icon/favicon.png>
    <link rel=icon type=image/png sizes=96x96 href=icon/icon-96x96.png>
    <link rel=icon type=image/png sizes=192x192 href=icon/icon-192x192.png>

    <meta property="og:site_name" content="{{ $jsapp['shop']['sitename'] }}" data-qmeta="ogSitename">

    <meta property="og:type" content="website" data-qmeta="ogType"/>
    @if(isset($jsapp['page']['featured_image']) && $jsapp['page']['featured_image'])
    <meta property="og:image" content="{{ $jsapp['page']['featured_image'] }}" data-qmeta="ogImage">
    @endif

    @isset($jsapp['page']['product_schema'])
      @include('product_schema')
    @endisset
  </head>
  <body class="bg-blue-grey-1">
  @if(isset($jsapp['page']['featured_image']) && $jsapp['page']['featured_image'])
      <link itemprop="thumbnailUrl" href="{{ $jsapp['page']['featured_image'] }}" >
      <span itemprop="thumbnail" itemscope itemtype="https://schema.org/ImageObject">
        <link itemprop="url" href="{{ $jsapp['page']['featured_image'] }}">
      </span>
    @endif
    <!-- DO NOT touch the following DIV -->
    <div id="q-app"></div>
  </body>
</html>
