import { defineConfig, normalizePath } from 'vite';
import liveReload from 'vite-plugin-live-reload';
import path from 'path';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
	plugins: [
		/**
		 * Live reload, para los archivos PHP
		 */
		liveReload(`${__dirname}/**/*.php`),

		/**
		 * Copia archivos estáticos
		 *
		 * @see https://github.com/sapphi-red/vite-plugin-static-copy
		 */
		viteStaticCopy({
			targets: [
				{
					src: normalizePath(path.resolve(__dirname, 'src/img')),
					dest: '',
				},
			],
		}),
	],

	/**
	 * Restablecer el project ROOT, para evitar un .html
	 *
	 * @see https://vitejs.dev/config/shared-options.html#root
	 */
	root: './src',

	/**
	 * URL base para ciertos archivos
	 */
	base: `/wp-content/themes/${path.basename(__dirname)}/assets/`,

	/**
	 * Build Options
	 *
	 * @see https://vitejs.dev/config/build-options.html
	 */
	build: {
		/**
		 * Compatibilidad para navegadores
		 *
		 * @see https://vitejs.dev/config/build-options.html#build-target
		 */
		target: 'modules', // es2018

		/**
		 * Carpeta destino
		 *
		 * @see https://vitejs.dev/config/build-options.html#build-outdir
		 */
		outDir: path.resolve(__dirname, 'assets'),

		/**
		 * Limpiar toda la carpeta de destino cuando se compile
		 *
		 * @see https://vitejs.dev/config/build-options.html#build-emptyoutdir
		 */
		emptyOutDir: true,

		/**
		 * Opciones para el compilador
		 *
		 * @see https://vitejs.dev/config/build-options.html#build-rollupoptions
		 */
		rollupOptions: {
			input: {
				main: path.resolve(__dirname, 'src/main.js'),
			},
			output: {
				// Nombres estáticos
				// entryFileNames: 'js/[name].js',

				// Nombres estáticos
				// assetFileNames: 'css/[name].[ext]',
			},
		},

		/**
		 * Comprimir archivos
		 *
		 * @see https://vitejs.dev/config/build-options.html#build-minify
		 */
		minify: true,

		/**
		 * Escribir en disco
		 *
		 * @see https://vitejs.dev/config/build-options.html#build-write
		 */
		write: true,

		/**
		 * Manifiesto de web
		 *
		 * @see https://vitejs.dev/config/build-options.html#build-manifest
		 */
		manifest: true,
	},

	/**
	 * Server Options
	 *
	 * @see https://vitejs.dev/config/server-options.html
	 */
	server: {
		// Puerto y forzar puerto de salida a 8081
		port: 8081,
		strictPort: true,

		// HTTPS
		https: false,

		// Requiere cors para hacer visitas desde un .test
		cors: true,
		origin: 'https://martaolga.com',

		/**
		 * Server HMR
		 *
		 * @see https://vitejs.dev/config/server-options.html#server-hmr
		 */
		hmr: {
			host: 'localhost',
		},
	},
});
