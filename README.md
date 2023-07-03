## Docker

Za lakše postavljanje i pokretanje zadatka moguće je koristi [Docker](https://docs.docker.com/engine/install/).

Zadatak sadrži datoteku `docker-compose.yml` koja će se koristiti za postavljanje kontejnera:
1. Pokretanjem `docker compose build` se postavljaju potrebni kontejneri
2. `docker compose up -d` pokreće kontejnere
3. `docker compose exec jela_svijeta_web_server php artisan migrate:fresh --seed` pokreće migracije i dodaje podatke u bazu podataka

Uspješnim pokretanjem bi web server trebao biti dostupan na http://localhost:8000/api/meals.

- Primjer upita http://localhost:8000/api/meals?per_page=5&tags=2&lang=hr&with=ingredients,category,tags&diff_time=1493902343&page=2.

## Ručno

Potrebno postaviti bazu podataka s korisnikom i bazom koje se definiraju i `.env` 
datoteci pod varijablama 
    `DB_CONNECTION`,
    `DB_PORT`,
    `DB_DATABASE`,
    `DB_USERNAME`,
    `DB_PASSWORD`.
Moguće koristiti `.env.example` za dijelomično popunjenu `.env` datoteku.

Prije pokretanja potrebno je imati instalirane 
[Php](https://www.php.net/manual/en/install.php) i [Composer](https://getcomposer.org/).

Prije prvog pokretanja mora se izvršiti naredba `composer install`.

Ako je sve dobro postavljeno naredbom `php artisan migrate:fresh --seed` će biti moguće popuniti bazu podataka.
Web server se pokreće naredbom `php artisan serve`.
