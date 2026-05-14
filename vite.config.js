import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
        host: "0.0.0.0", // This exposes the server to the Docker network
        port: 5173,
        hmr: {
            host: "localhost", // Tells your browser where to look for the hot-reloading socket
        },
    },
});
