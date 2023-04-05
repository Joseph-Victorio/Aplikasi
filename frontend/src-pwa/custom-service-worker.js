/*
 * This file (which will be your service worker)
 * is picked up by the build system ONLY if
 * quasar.conf > pwa > workboxPluginMode is set to "InjectManifest"
 */
import { precacheAndRoute } from 'workbox-precaching';
import {registerRoute} from 'workbox-routing';
import {StaleWhileRevalidate} from 'workbox-strategies';

precacheAndRoute(self.__WB_MANIFEST);

self.__WB_DISABLE_DEV_LOGS = true

// Cache Google Fonts with a stale-while-revalidate strategy, with
// a maximum number of entries.
registerRoute(
  ({url}) => url.origin === 'https://fonts.googleapis.com',
  new StaleWhileRevalidate({
    cacheName: 'google-fonts-stylesheets',
  })
);
