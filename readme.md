# Vue Base
A true WordPress theme with the guts ripped out and replaced with Vue. Based on the [Vuejs](https://github.com/EvanAgee/vuejs-wordpress-theme-starter) WP starter theme.

## Code Organization
All of the code you're going to edit is located in `/src/`. From there it's broken into a few logical directories.

- `/src`
  - `/api` for API requests
  - `/assets` for images mostly
  - `/components` Vue components
  - `/router` vue-router directives
  - `/server` server side php logic for altering the wp-json api output
  - `/store` vuex store and modules
  - `/styles` SCSS styles
  - `/vendor` 3rd party scripts and libraries

All scripts and styles in `/src` are compiled down to the `/dist` directory, which is what you will deploy. **When you're ready to deploy don't deploy the src/ directory.**

## External References
- [Creating Vuex Modules](https://vuex.vuejs.org/en/modules.html)
- [vue-router](https://github.com/vuejs/vue-router)
- [WordPress REST API](http://v2.wp-api.org/)
