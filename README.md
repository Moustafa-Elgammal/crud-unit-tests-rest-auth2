<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>



## About The Project

This is a small task to test How To build CRUD Moduls

- Students 
- Schools

### install and run the product
    
- install php dependencies by:
  
    ```composer install ```
- install breeze 
  
    ```npm install ``` 

    ```npm run dev```
  
- migrate the database table with:

- ```php artisan migrate```

- database seeds:
  
    ```php artisan db:seed ```

- Laravel Passport Oauth 2:
  
```
    php artisan passport:install
    php artisan passport:keys
 ```

- run the project:

```
    php artisan serve
 ```


### Admin  Area

To manage the Students and Schools

### Cli Command

this command to fix order of student with deletes done:
    
      php artisan students:order

### Restful Api 
use the generated clients to log in the admin user

- You can find the postman collection by 

- file:  schools_students_Api_crud.postman_collection.json


