Default Laravel project setup, local development with Valet.

Make sure you have actual version of **composer**. Then run _`composer install`_ to install all project dependencies.

Run command _`php artisan storage:link`_ to setup basic file storage.

Run command _`php artisan migrate`_ to run all migrations in order to setup database. Before that, make sure you configured DB connection in .env file.

Run command _`php artisan passport:install`_ to get security tokens, which will be used in authorization
