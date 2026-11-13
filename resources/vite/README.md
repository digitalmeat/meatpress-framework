# @meatpress/vite-config

The Vite configuration shared by Meatpress themes, plugins and modules. It lives in the framework repository and is installed from there (it is not published to npm):

```json
"devDependencies": {
  "@meatpress/vite-config": "github:digitalmeat/meatpress-framework#semver:^13.1&path:/resources/vite"
}
```

```js
// vite.config.js
import { defineConfig } from 'vite';
import meatpress from '@meatpress/vite-config';

export default defineConfig({
  plugins: [meatpress({ type: 'theme', tailwind: false, themeJson: false, blocks: false })],
});
```

| `type` | Build directory | Hot file | Default port |
| --- | --- | --- | --- |
| `theme` | `public/build/theme/<name>` | `public/<name>.hot` | 5173 |
| `plugin` | `public/build/plugin/<name>` | `public/<name>.hot` | 5174 |
| `module` | `public/build/module/<kebab-name>` | `public/<kebab-name>.hot` | 5175 |

Options are documented in `src/index.d.ts`.
