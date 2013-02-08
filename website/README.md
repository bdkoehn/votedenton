# Hybrid Enterprise

Hybrid Enterprise is a clean, extensible, and ready to use framework for the start of a WordPress website. It includes a theme, and several "must use" plugins.

* The repository structure and config files are based on [WordPress Skeleton](https://github.com/markjaquith/WordPress-Skeleton).
* The theme utilizes [Hybrid Core](https://github.com/justintadlock/hybrid-core) and [Twitter Bootstrap](https://github.com/twitter/bootstrap).

## Theme

The included theme is barebones. It simply provides a starting point. There are not styles other than what Bootstrap includes.

## Plugins

* *enterprise.php* adds meta boxes to `post` and `page` post types for CSS and JS input.
* *wp-env-domain.php* filters database instances of a domain with the one defined with `ENV_DOMAIN`.
* *register-theme-directory.php* registers the theme directory in `wp` submodule as an additional one and makes native themes functional out of the box.

This doesn't devieate from a standard WordPress install plus a Hybrid theme, but it will eventually become a complete solution for the beginning of any professional WordPress install.
