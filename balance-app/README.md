# Дмитрий Батов
# Тестовое задание — Приложение для работы с балансом пользователей

# Стек
Backend	PHP 8.2 + Laravel 10
Database	PostgreSQL 15

# docker compose up -d --build
После сборки будут доступны контейнеры:
Контейнер	   Назначение	        Порт
laravel-app	 Laravel-приложение	    8000
postgres-db	 База данных PostgreSQL	5432

# docker compose exec app php artisan test
Тестируется 
1. Пополнение счёта
2. Снятие средств
3. Переводы между пользователями
4. Валидация и обработка ошибок

# создать тестовых пользователей
docker compose exec app php artisan tinker
App\Models\User::create(['name' => 'Дима', 'email' => 'dima@mail.com', 'password' => bcrypt('123456')]);
App\Models\User::create(['name' => 'Олег', 'email' => 'oleg@mail.com', 'password' => bcrypt('123456')]);
exit


# Примеры запросов
    Пополнение
curl -X POST http://localhost:8000/api/deposit \
  -H "Content-Type: application/json" \
  -d '{"user_id":1, "amount":500, "comment":"Пополнение через карту"}'

    Снятие средств
curl -X POST http://localhost:8000/api/withdraw \
  -H "Content-Type: application/json" \
  -d '{"user_id":1, "amount":100}'

  Перевод средств 
  curl -X POST http://localhost:8000/api/transfer \
  -H "Content-Type: application/json" \
  -d '{"from_user_id":1, "to_user_id":2, "amount":50, "comment":"Перевод другу"}'

    Проверка баланса 
http://localhost:8000/api/balance/1


