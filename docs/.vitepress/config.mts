import { defineConfig } from 'vitepress';

export default defineConfig({
  title: 'Meatpress',
  description: 'WordPress sobre Laravel para los proyectos de MEAT Code',
  lang: 'es-CL',
  // Set DOCS_BASE when the site is served from a sub-path (e.g. a GitHub Pages project site).
  base: process.env.DOCS_BASE ?? '/',
  cleanUrls: true,
  lastUpdated: true,
  head: [['meta', { name: 'robots', content: 'noindex, nofollow' }]],
  themeConfig: {
    nav: [
      { text: 'Guía', link: '/guide/getting-started' },
      { text: 'Convenciones', link: '/guide/conventions' },
      { text: 'Desde Themosis', link: '/guide/migrating-from-themosis' },
    ],
    sidebar: [
      {
        text: 'Getting started',
        items: [
          { text: 'What is Meatpress', link: '/guide/what-is-meatpress' },
          { text: 'New project', link: '/guide/getting-started' },
          { text: 'Docker', link: '/guide/docker' },
          { text: 'Project structure', link: '/guide/structure' },
        ],
      },
      {
        text: 'Building a site',
        items: [
          { text: 'Theme', link: '/guide/theme' },
          { text: 'Hooks, post types and taxonomies', link: '/guide/code' },
          { text: 'Plugins', link: '/guide/plugins' },
          { text: 'Tracking: GTM or GA4', link: '/guide/tracking' },
        ],
      },
      {
        text: 'Reference',
        items: [
          { text: 'Conventions', link: '/guide/conventions' },
          { text: 'Quality tooling', link: '/guide/tooling' },
          { text: 'Updating and releasing', link: '/guide/releasing' },
          { text: 'Migrating from Themosis', link: '/guide/migrating-from-themosis' },
        ],
      },
    ],
    socialLinks: [{ icon: 'github', link: 'https://github.com/digitalmeat/meatpress-base' }],
    search: { provider: 'local' },
    outline: { label: 'En esta página' },
    docFooter: { prev: 'Anterior', next: 'Siguiente' },
    lastUpdated: { text: 'Actualizado' },
    footer: { message: 'MEAT Code — uso interno' },
  },
});
