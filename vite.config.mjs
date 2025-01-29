import { defineConfig } from 'vite';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import path from 'path';

export default defineConfig({
    plugins: [
        viteStaticCopy({
            targets: [
                { src: path.resolve(__dirname, 'node_modules/jquery/dist/jquery.min.js'), dest: 'js' },
                { src: path.resolve(__dirname, 'node_modules/jquery-ui/dist/jquery-ui.min.js'), dest: 'js' },
                { src: path.resolve(__dirname, 'node_modules/bootstrap/dist/js/bootstrap.min.js'), dest: 'js' },
                { src: path.resolve(__dirname, 'node_modules/summernote/dist/summernote.min.js'), dest: 'js/summernote' },
                { src: path.resolve(__dirname, 'node_modules/summernote/dist/lang/summernote-de-DE.min.js'), dest: 'js/summernote' },
                { src: path.resolve(__dirname, 'node_modules/summernote/dist/summernote.min.css'), dest: 'css' },
                { src: path.resolve(__dirname, 'node_modules/summernote/dist/font'), dest: 'css' },
                { src: path.resolve(__dirname, 'node_modules/font-awesome/css/font-awesome.min.css'), dest: 'css' },
                { src: path.resolve(__dirname, 'node_modules/font-awesome/fonts'), dest: '' },
                { src: path.resolve(__dirname, 'build/img/*'), dest: 'img' },
            ],
        }),
    ],
    build: {
        outDir: path.resolve(__dirname, 'assets/out/src'),
        rollupOptions: {
            preserveEntrySignatures: 'strict',
            input: {
                summernoteInitJs: path.resolve(__dirname, 'build/js/summernote/init.js'),
                backendCss: path.resolve(__dirname, 'build/less/backend_editor.less'),
            },
            output: {
                entryFileNames: (chunk) => {
                    const nameMap = {
                        summernoteInitJs: 'js/summernote/init.min.js',
                    };
                    return nameMap[chunk.name] || 'assets/js/[name].[hash].js'; // Default fallback
                },
                assetFileNames: (assetInfo) => {
                    const nameMap = {
                        backendCss: 'css/backend.min.css',
                    };
                    const inputKey = assetInfo.name && Object.keys(nameMap).find((key) => assetInfo.name.includes(key));

                    return inputKey ? nameMap[inputKey] : 'assets/[name].[hash].[ext]'; // Default fallback
                },
            },
            treeshake: false,
        },
        minify: 'esbuild',
    },
});
