/**
 * Service Worker for Marreta
 *
 * Acts as a lightweight network proxy and PWA share-target handler.
 */

self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});

self.addEventListener('share_target', (event) => {
    event.respondWith((async () => {
        const formData = await event.request.formData();
        const url = formData.get('url') || '';
        const redirectUrl = `/p/${encodeURIComponent(url)}`;

        return Response.redirect(redirectUrl, 303);
    })());
});
