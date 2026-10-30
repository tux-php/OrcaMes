// Service worker do OrcaMes. Existe para o app ser instalável e para mostrar um aviso
// quando o celular está sem internet. De propósito não guarda páginas nem dados: tudo
// o que aparece vem sempre do servidor, então os valores nunca ficam desatualizados.
const CACHE = 'orcames-sem-conexao-v2';
const PAGINA_SEM_CONEXAO = '/offline.html';

self.addEventListener('install', (evento) => {
    evento.waitUntil(caches.open(CACHE).then((cache) => cache.add(PAGINA_SEM_CONEXAO)));
    self.skipWaiting();
});

self.addEventListener('activate', (evento) => {
    evento.waitUntil(
        caches.keys()
            .then((nomes) => Promise.all(nomes.filter((nome) => nome !== CACHE).map((nome) => caches.delete(nome))))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (evento) => {
    if (evento.request.mode !== 'navigate') {
        return;
    }

    evento.respondWith(fetch(evento.request).catch(() => caches.match(PAGINA_SEM_CONEXAO)));
});
