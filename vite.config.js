import { defineConfig } from 'vite';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import eslint from 'vite-plugin-eslint';
import path from 'path';

export default defineConfig({
    plugins: [
        viteStaticCopy({
            targets: [
                { src: path.resolve(__dirname, 'node_modules/jquery/dist/jquery.min.js'), dest: 'js' },
                { src: path.resolve(__dirname, 'node_modules/jquery-ui/dist/jquery-ui.min.js'), dest: 'js' },
                { src: path.resolve(__dirname, 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'), dest: 'js' },
                { src: path.resolve(__dirname, 'node_modules/summernote/dist/summernote-bs5.min.js'), dest: 'js/summernote', rename: 'summernote.min.js' },
                { src: path.resolve(__dirname, 'node_modules/summernote/dist/lang/summernote-de-DE.min.js'), dest: 'js/summernote' },
                { src: path.resolve(__dirname, 'node_modules/summernote/dist/summernote-bs5.min.css'), dest: 'css', rename: 'summernote.min.css' },
                { src: path.resolve(__dirname, 'node_modules/summernote/dist/font'), dest: 'css' },
                { src: path.resolve(__dirname, 'node_modules/bootstrap-icons/font/bootstrap-icons.min.css'), dest: 'css' },
                { src: path.resolve(__dirname, 'node_modules/bootstrap-icons/font/fonts'), dest: 'css' },
                { src: path.resolve(__dirname, 'node_build/img/*'), dest: 'img' }
            ]
        }),
        eslint({
            overrideConfigFile: path.resolve(__dirname, 'node_build/js/eslint.config.js'),
            failOnError: true,
            failOnWarning: false,
        })
    ],
    build: {
        outDir: path.resolve(__dirname, 'assets/out/src'),
        minify: true,
        sourcemap: true,
        rollupOptions: {
            preserveEntrySignatures: 'strict',
            input: {
                ddoesummernote: path.resolve(__dirname, 'node_build/js/summernote/init.js'),
                overlay: path.resolve(__dirname, 'node_build/js/summernote/overlay.js'),
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
