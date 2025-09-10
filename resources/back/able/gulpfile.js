/* Able → Laravel build to public/admin/theme1
 * - cleans dest
 * - copies theme assets (fonts/images/js)
 * - compiles SCSS (explicit entries): style.scss, style-preset.scss
 * - copies vendor plugins used in base-admin.blade
 * - rewrites @import "bootstrap/scss/…" to absolute path (rock-solid)
 */

const gulp         = require('gulp');
const del          = require('del');
const merge        = require('merge-stream');
const autoprefixer = require('gulp-autoprefixer');
const sourcemaps   = require('gulp-sourcemaps');
const uglify       = require('gulp-uglify');
const cssmin       = require('gulp-cssmin');
const replace      = require('gulp-replace');
const nodePath     = require('path');

// Dart Sass via gulp-sass
const gulpSass = require('gulp-sass')(require('sass'));

const DEST_BASE = process.env.DEST_BASE || '../../../public/admin/theme1';

const paths = {
    src: {
        scssEntries: [
            'src/assets/scss/style.scss',
            'src/assets/scss/style-preset.scss'
        ],
        scssAll: 'src/assets/scss/**/*.scss',
        js: [
            'src/assets/js/**/*.js',
            '!src/assets/js/plugins/**'
        ],
        fonts:  'src/assets/fonts/**/*',
        images: 'src/assets/images/**/*',
        cssFallback: 'src/assets/css/*.css'
    },
    dest: {
        css:        `${DEST_BASE}/assets/css`,
        js:         `${DEST_BASE}/assets/js`,
        pluginsJs:  `${DEST_BASE}/assets/js/plugins`,
        pluginsCss: `${DEST_BASE}/assets/css/plugins`,
        fonts:      `${DEST_BASE}/assets/fonts`,
        images:     `${DEST_BASE}/assets/images`
    }
};

// Resolve bootstrap dir robustly (local or hoisted)
function modDir(name) {
    return nodePath.dirname(require.resolve(`${name}/package.json`));
}
const BOOTSTRAP_DIR = modDir('bootstrap');
const BOOTSTRAP_SCSS = nodePath.join(BOOTSTRAP_DIR, 'scss');

// Prebuild include paths (also set SASS_PATH for good measure)
const includePaths = [
    nodePath.resolve(__dirname),                 // <-- theme root, enables 'node_modules/...'
    nodePath.resolve(__dirname, 'src/assets/scss'),
    nodePath.resolve(__dirname, 'node_modules'),
    nodePath.resolve(__dirname, '../../../../node_modules'),
    BOOTSTRAP_SCSS
];
process.env.SASS_PATH = includePaths.join(nodePath.delimiter);

const sassOptions = {
    includePaths,
    quietDeps: true,
    silenceDeprecations: ['import', 'color-functions', 'global-builtin', 'legacy-js-api']
};

// Replace @import "bootstrap/scss/xxx" → "@import '/abs/.../bootstrap/scss/xxx'"
function rewriteBootstrapImports(stream) {
    return stream.pipe(
        replace(
            /@import\s+["'](?:node_modules\/)?bootstrap\/scss\/([^"']+)["'];?/g,
            (_m, file) => `@import "${nodePath.join(BOOTSTRAP_SCSS, file)}";`
        )
    );
}


// 1) Clean everything
gulp.task('clean', (cb) => {
    del.sync([`${DEST_BASE}/**/*`], { force: true });
    cb();
});

// 2) Copy theme assets
gulp.task('assets', function () {
    const fonts  = gulp.src(paths.src.fonts).pipe(gulp.dest(paths.dest.fonts));
    const images = gulp.src(paths.src.images).pipe(gulp.dest(paths.dest.images));
    const jsCore = gulp.src(paths.src.js).pipe(gulp.dest(paths.dest.js)); // script.js, theme.js, icon/custom-font.js, etc.
    return merge(fonts, images, jsCore);
});

// 3) Vendors used by base-admin.blade (+ stacks)
gulp.task('vendors', function () {
    const vendorJs = gulp
    .src([
        'node_modules/@popperjs/core/dist/umd/popper.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/simplebar/dist/simplebar.min.js',
        'node_modules/feather-icons/dist/feather.min.js',
        'node_modules/choices.js/public/assets/scripts/choices.min.js',
        'node_modules/axios/dist/axios.js',
        'node_modules/sweetalert2/dist/sweetalert2.js'
    ])
    .pipe(gulp.dest(paths.dest.pluginsJs));

    const vendorCss = gulp
    .src([
        'node_modules/simplebar/dist/simplebar.min.css',
        'node_modules/choices.js/public/assets/styles/choices.min.css',
        'node_modules/sweetalert2/dist/sweetalert2.css'
    ])
    .pipe(gulp.dest(paths.dest.pluginsCss));

    return merge(vendorJs, vendorCss);
});

// 4) SCSS → CSS (dev)
gulp.task('sass', function () {
    return rewriteBootstrapImports(
        gulp.src(paths.src.scssEntries, { allowEmpty: true })
    )
    .pipe(sourcemaps.init())
    .pipe(gulpSass(sassOptions).on('error', gulpSass.logError))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(paths.dest.css));
});

// 5) SCSS → CSS (prod)
gulp.task('min-css', function () {
    return rewriteBootstrapImports(
        gulp.src(paths.src.scssEntries, { allowEmpty: true })
    )
    .pipe(gulpSass(sassOptions).on('error', gulpSass.logError))
    .pipe(autoprefixer())
    .pipe(cssmin())
    .pipe(gulp.dest(paths.dest.css));
});

// 6) Fallback: copy any prebuilt CSS if present
gulp.task('css-fallback', function () {
    return gulp.src(paths.src.cssFallback, { allowEmpty: true })
    .pipe(gulp.dest(paths.dest.css));
});

// 7) (optional) Minify theme JS core
gulp.task('min-js', function () {
    return gulp.src(paths.src.js)
    .pipe(uglify())
    .pipe(gulp.dest(paths.dest.js));
});

// 8) Watch (dev)
gulp.task('watch', function () {
    gulp.watch(paths.src.scssAll, gulp.series('sass'));
    gulp.watch(paths.src.js, gulp.series('assets'));
    gulp.watch(paths.src.fonts, gulp.series('assets'));
    gulp.watch(paths.src.images, gulp.series('assets'));
    gulp.watch(paths.src.cssFallback, gulp.series('css-fallback'));
});

// Public tasks
gulp.task('build', gulp.series('clean', 'assets', 'vendors', 'sass', 'css-fallback'));
gulp.task('build-prod', gulp.series('clean', 'assets', 'vendors', 'min-css', 'css-fallback', 'min-js'));
