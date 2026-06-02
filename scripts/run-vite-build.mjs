import { createHash } from 'node:crypto';
import { mkdir, readFile, rm, writeFile } from 'node:fs/promises';
import path from 'node:path';
import postcss from 'postcss';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';
import tailwindConfig from '../tailwind.config.js';

const buildDir = path.resolve('public/build');
const assetsDir = path.join(buildDir, 'assets');
const sourceCss = path.resolve('resources/css/app.css');
const sourceJs = path.resolve('resources/js/app.js');

function shortHash(content) {
    return createHash('sha1').update(content).digest('hex').slice(0, 8);
}

await rm(buildDir, { recursive: true, force: true });
await mkdir(assetsDir, { recursive: true });

const [sourceCssContent, jsContent] = await Promise.all([
    readFile(sourceCss, 'utf8'),
    readFile(sourceJs, 'utf8'),
]);

const cssResult = await postcss([
    tailwindcss(tailwindConfig),
    autoprefixer,
]).process(sourceCssContent, {
    from: sourceCss,
    to: path.join(assetsDir, 'app.css'),
});

const cssContent = cssResult.css;
const cssFileName = `app-${shortHash(cssContent)}.css`;
const jsFileName = `app-${shortHash(jsContent)}.js`;

await Promise.all([
    writeFile(path.join(assetsDir, cssFileName), `${cssContent}\n`, 'utf8'),
    writeFile(path.join(assetsDir, jsFileName), `${jsContent}\n`, 'utf8'),
]);

const manifest = {
    'resources/css/app.css': {
        file: `assets/${cssFileName}`,
        src: 'resources/css/app.css',
        isEntry: true,
    },
    'resources/js/app.js': {
        file: `assets/${jsFileName}`,
        src: 'resources/js/app.js',
        isEntry: true,
    },
};

await writeFile(
    path.join(buildDir, 'manifest.json'),
    `${JSON.stringify(manifest, null, 2)}\n`,
    'utf8',
);
