import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/seller.js',

                // BUYER
                'resources/js/buyer.js',
                'resources/js/buyer-products.js',
                'resources/js/buyer-shop.js',
                'resources/js/buyer-product-details.js',
                'resources/js/buyer-cart.js',
                'resources/js/buyer-checkout.js',
                'resources/js/buyer-orders.js',
                'resources/js/buyer-messages.js',
                'resources/js/buyer-rewards.js',
                'resources/js/buyer-account.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});