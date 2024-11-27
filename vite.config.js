import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/dragdroptable.js", 
                "resources/js/sortcols.js", 
                "resources/js/modal.js",
                "resources/js/cogs.js",
            ],
            refresh: true,
        }),
    ],
});
