import { defineConfig } from 'vite';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import eslint from 'vite-plugin-eslint';
import path from 'path';

export default defineConfig({
    css: {
        preprocessorOptions: {
            scss: {
                // Silence deprecation warnings coming from dependencies (Bootstrap)
                quietDeps: true,
                silenceDeprecations: [
                    'import',
                ],
                api: 'modern'
            }
        },
    },
    plugins: [
        viteStaticCopy({
            targets: [
                { src: path.resolve(import.meta.dirname, 'node_modules/jquery/dist/jquery.min.js'), dest: 'js', rename: { stripBase: true } },
                { src: path.resolve(import.meta.dirname, 'node_modules/jquery-ui/dist/jquery-ui.min.js'), dest: 'js', rename: { stripBase: true } },
                { src: path.resolve(import.meta.dirname, 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'), dest: 'js', rename: { stripBase: true } },
                { src: path.resolve(import.meta.dirname, 'node_modules/summernote/dist/summernote-bs5.min.js'), dest: 'js/summernote', rename: { stripBase: true, name: 'summernote.min.js' } },
                { src: path.resolve(import.meta.dirname, 'node_modules/summernote/dist/lang/summernote-de-DE.min.js'), dest: 'js/summernote', rename: { stripBase: true } },
                { src: path.resolve(import.meta.dirname, 'node_modules/dompurify/dist/purify.min.js'), dest: 'js/summernote', rename: { stripBase: true } },
                { src: path.resolve(import.meta.dirname, 'node_modules/summernote/dist/summernote-bs5.min.css'), dest: 'css', rename: { stripBase: true, name: 'summernote.min.css' } },
                { src: path.resolve(import.meta.dirname, 'node_modules/summernote/dist/font'), dest: 'css/font', rename: { stripBase: true } },
                { src: path.resolve(import.meta.dirname, 'node_modules/bootstrap-icons/font/bootstrap-icons.min.css'), dest: 'css', rename: { stripBase: true } },
                { src: path.resolve(import.meta.dirname, 'node_modules/bootstrap-icons/font/fonts'), dest: 'css/fonts', rename: { stripBase: true } },
                { src: path.resolve(import.meta.dirname, 'node_build/img/*'), dest: 'img', rename: { stripBase: true } }
            ]
        }),
        eslint({
            overrideConfigFile: path.resolve(import.meta.dirname, 'node_build/js/eslint.config.js'),
            failOnError: true,
            failOnWarning: false,
        })
    ],
    build: {
        outDir: path.resolve(import.meta.dirname, 'assets/out/src'),
        minify: true,
        sourcemap: true,
        rollupOptions: {
            preserveEntrySignatures: 'strict',
            input: {
                ddoesummernote: path.resolve(import.meta.dirname, 'node_build/js/summernote/init.js'),
                overlay: path.resolve(import.meta.dirname, 'node_build/js/summernote/overlay.js'),
            },
            output: {
                entryFileNames: 'js/summernote/[name].min.js',
                chunkFileNames: 'js/[name].min.js',
                assetFileNames: 'css/[name].min.[ext]',
            },
            treeshake: false,
        },
    },
});
