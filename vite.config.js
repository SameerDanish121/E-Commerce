import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import os from 'os';
import vue from '@vitejs/plugin-vue'
function getLocalNetworkIP() {
    const interfaces = os.networkInterfaces();
    for (const name in interfaces) {
        for (const iface of interfaces[name]) {
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return '127.0.0.1';
}

const localIP = getLocalNetworkIP();



export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
        build: {
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                customer: 'resources/js/customer_app.js'
            }
        }
    },
     resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    server: {
        host: localIP,
        port: 5173,
        strictPort: true,
        hmr: {
            host: localIP,
        },
    },
    
});

// import { defineConfig } from 'vite'
// import laravel from 'laravel-vite-plugin'
// import vue from '@vitejs/plugin-vue'

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//         vue(),
//     ],
//         build: {
//         rollupOptions: {
//             input: {
//                 app: 'resources/js/app.js',
//                 customer: 'resources/js/customer_app.js'
//             }
//         }
//     },
//     resolve: {
//         alias: {
//             vue: 'vue/dist/vue.esm-bundler.js',
//         },
//     },
    
// });
