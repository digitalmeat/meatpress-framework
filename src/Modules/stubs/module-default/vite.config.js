import { defineConfig } from 'vite';
import meatpress from '@meatpress/vite-config';

// Builds into public/build/module/%module_slug%, where Meatpress serves the module.<slug> asset container
export default defineConfig({
    plugins: [meatpress({ type: 'module', name: '%module_slug%' })],
});
