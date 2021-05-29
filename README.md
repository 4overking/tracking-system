Service links tracking system
===================

### :bomb: Installation and first launch

1. Clone the project
    ```bash
    git git@github.com:4overking/tracking-system.git
    ```

2. Run containers and initialize

   **On linux:**
   
    ```bash
    make initialize
    ```
   **On other systems without Makefile support:**
    ```cmd
    docker-compose up
    docker-compose exec php composer install
    docker-compose exec php composer bin/console doctrine:schema:update --force
    ```

3. Run tests

   **On linux:**

    ```bash
    make run_tests
    ```
   **On other systems without Makefile support:**
    ```cmd
    docker-compose exec php ./vendor/bin/phpunit
    ```

### :pencil: Regular launch for development

   **On linux:**
   
       ```bash
       make up
       ```
   **On other systems without Makefile support:**
   ```cmd
   docker-compose up -d --build
   ```

### :beer: Opening PHP container console

####It should be executed after first launch or regular launch commands

   **On linux:**
   
       ```bash
       make console
       ```
   **On other systems without Makefile support:**
   ```cmd
   docker-compose exec php bash
   ```
