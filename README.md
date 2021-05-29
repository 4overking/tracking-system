Service links tracking system
===================
#### Before you start please ensure your local 80 port is FREE!

### :bomb: Installation and first launch

1. Clone the project
 ```bash
 git clone git@github.com:4overking/tracking-system.git
 ```

2. Run containers and initialize

   **On linux:**
   
 ```bash
 make initialize
 ```
   
   **On other systems without Makefile support:**

```cmd
docker-compose up -d --build
docker-compose exec php composer install
docker-compose exec php composer bin/console doctrine:schema:update --force
```

3. Run tests (DB will be cleaned)

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

#### It should be executed after first launch or regular launch commands

   **On linux:**
   
 ```bash
 make console
 ```
   
   **On other systems without Makefile support:**
```cmd
docker-compose exec php bash
```

### :inbox_tray: Checking by yourself

* Log organic record

```bash
curl --header "Content-Type: application/json" \
  --request POST \
  --data '{"client_id":"user15","User-Agent":"Firefox 59", "document.location": "https://shop.com/products/?id=2", "document.referer": "https://yandex.ru/search/?q=buy ab happiness", "date": "2018-04-03T07:59:13.286000Z"}' \
  http://localhost/api/visit
```

* Log ours service record

```bash
curl --header "Content-Type: application/json" \
  --request POST \
  --data '{"client_id":"user15","User-Agent":"Firefox 59", "document.location": "https://shop.com/products/?id=2", "document.referer": "https://referal.ours.com/?ref=123hexcode", "date": "2018-04-03T08:59:13.286000Z"}' \
  http://localhost/api/visit
```

* Log "paid for the goods" record

```bash
curl --header "Content-Type: application/json" \
  --request POST \
  --data '{"client_id":"user15","User-Agent":"Firefox 59", "document.location": "https://shop.com/checkout", "document.referer": "https://shop.com/products/?id=2”", "date": "2018-04-03T08:59:13.286000Z"}' \
  http://localhost/api/visit
```

* Get result where our record pointed to buying (after executing previous requests)
  
```bash
curl --header "Content-Type: application/json" \
  --request GET \
  http://localhost/api/purchases
```
Authorization for this method was not implemented
