## About the Crawler

The application was built on top of Laravel 9, utilizing newer features of PHP 8 in code e.g. `str_starts_with` and more, depends on Curl 
for it crawling capabilities (could have just used Guzzle, but where's the fun in that?), with a simple inertiaJs frontend (SSR) with AJAX
calls to the backend.

### Running locally
Before running this locally, make sure your have docker installed locally. It might be best also if you had composer / PHP installed locally 
merely to ensure that your dependencies are ready.

- Clone repo and enter repo folder.
- run `composer install && ./vendor/bin/sail up -d`. This will take a pretty minute or two.
- run `./vendor/bin/sail npm run i && ./vendor/bin/sail npm run dev`.
- The application can then be loaded on a browser with http://localhost
