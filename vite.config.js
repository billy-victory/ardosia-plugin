import { defineConfig } from "vite";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import { resolve } from "path";
import tailwindcss from "@tailwindcss/vite";

// https://vitejs.dev/config/
export default defineConfig({
	plugins: [svelte(), tailwindcss()],
	build: {
		// Output directory for the production build
		outDir: "dist",

		// Generate manifest for proper asset handling
		manifest: true,

		// Configure rollup options
		rollupOptions: {
			input: {
				main: resolve(__dirname, "app/src/main.js"),
			},
			output: {
				entryFileNames: "assets/[name]-[hash].js",
				chunkFileNames: "assets/[name]-[hash].js",
				assetFileNames: "assets/[name]-[hash].[ext]",
			},
		},
	},
});
