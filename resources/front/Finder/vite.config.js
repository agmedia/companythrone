import { defineConfig } from 'vite'
import path from 'node:path'
import fs from 'node:fs'
import { viteStaticCopy } from 'vite-plugin-static-copy'

const rootDir     = __dirname
const outDir      = path.resolve(__dirname, '../../../public/theme1')
// kamo ćemo smjestiti vendore da odgovaraju HTML-u teme:
const vendorBase  = 'assets/vendor'
// kamo ćemo smjestiti ostale slike:
const destImages  = 'images'

// pomoćnici
const exists = (p) => fs.existsSync(p)
const projectRoot = path.resolve(rootDir, '../../..') // root laravel projekta
const nm = (...p) => path.join(projectRoot, 'node_modules', ...p)

function copyTargetsFromFinder(root) {
    const pairs = [
        ['assets', 'assets'],
        ['css', 'css'],
        ['scss', 'scss'],
        ['js', 'js'],
        ['img', 'img'],
        ['images', 'images'],
        ['fonts', 'fonts'],
        ['vendor', 'vendor'],
        ['dist', 'dist'],
        ['static', 'static'],
    ]

    const t = []
    for (const [dir, dest] of pairs) {
        const full = path.join(root, dir)
        if (exists(full)) t.push({ src: path.join(full, '**/*'), dest })
    }

    // HTML (referentno, nije nužno ali zgodno za pregled u /public/theme1)
    const html = path.join(root, 'home-contractors.html')
    if (exists(html)) t.push({ src: html, dest: '.' })

    // sve slike fallback (pokriva i CSS url())
    t.push({ src: path.join(root, '**/*.{png,jpg,jpeg,webp,avif,svg,gif,ico}'), dest: destImages })

    return t
}

function copyVendorsFromNodeModules() {
    const t = []

    // SWIPER (ono što HTML očekuje: assets/vendor/swiper/…)
    if (exists(nm('swiper'))) {
        const dest = path.join(vendorBase, 'swiper')
        const base = nm('swiper') // u novijim verzijama je bundl pod /package/
        // probaj tipične staze:
        const candidates = [
            ['swiper-bundle.min.js', 'dist/swiper-bundle.min.js'],
            ['swiper-bundle.min.css', 'dist/swiper-bundle.min.css'],
            // fallback za neke rasporede paketa:
            ['swiper-bundle.min.js', 'swiper-bundle.min.js'],
            ['swiper-bundle.min.css', 'swiper-bundle.min.css'],
        ]
        for (const [name, rel] of candidates) {
            const src = path.join(base, rel)
            if (exists(src)) t.push({ src, dest })
        }
    }

    // GLightbox (često korišten u Finder temama)
    if (exists(nm('glightbox'))) {
        const dest = path.join(vendorBase, 'glightbox')
        const base = nm('glightbox', 'dist')
        for (const rel of ['glightbox.min.js', 'css/glightbox.min.css']) {
            const src = path.join(base, rel)
            if (exists(src)) t.push({ src, dest })
        }
    }

    // AOS (ako se koristi)
    if (exists(nm('aos'))) {
        const dest = path.join(vendorBase, 'aos')
        const base = nm('aos', 'dist')
        for (const rel of ['aos.js', 'aos.css']) {
            const src = path.join(base, rel)
            if (exists(src)) t.push({ src, dest })
        }
    }

    // DODAJ OVDJE druge vendore po potrebi…

    return t
}

export default defineConfig({
    root: rootDir,
    base: '/theme1/',
    publicDir: false,
    build: {
        outDir,
        emptyOutDir: true,
        // Nemoj koristiti home-contractors.html kao input da Vite ne pokušava bundlati <script> bez type="module":
        rollupOptions: {
            // minimalni placeholder — kreiraj praznu datoteku ako treba
            input: path.join(rootDir, '_placeholder.html'),
        },
    },
    plugins: [
        viteStaticCopy({
            targets: [
                ...copyTargetsFromFinder(rootDir),
                ...copyVendorsFromNodeModules(),
            ],
        }),
    ],
})
