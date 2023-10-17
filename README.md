# G4T Mock Interface Laravel Package

![Packagist Version](https://img.shields.io/packagist/v/hussein4alaa/g4t-mock-interface-laravel)
![GitHub](https://img.shields.io/github/license/hussein4alaa/g4t-mock-interface-laravel)

The G4T Mock Interface Laravel Package is a versatile and essential tool for managing mock interfaces within your Laravel applications. This package streamlines the process of creating and managing mock interfaces, making it invaluable for developers engaged in API testing and development.

## Installation

You can easily integrate this package into your Laravel project using Composer. To do so, execute the following command:

```bash
composer require g4t/mock-interface
```

## Usage
The G4T Mock Interface Laravel Package offers a wide array of features to facilitate the management of mock interfaces in your Laravel application. Here's how you can get started:

Create a new interface:
```bash
php artisan interface:create UserInterface
```

To create an interface with a schema file and CRUD functions:
```bash
php artisan interface:create UserInterface --all
```

Create a schema with a schema file and CRUD functions:
```bash
php artisan schema:create UserInterface
```
You can also use the --model and --interface options with the above commands.

Here's an example of an interface and how it can be utilized:
```bash
<?php

namespace App\Mock\Interfaces;
use Illuminate\Http\Request;

interface UserInterface
{
    /**
     * @route api/user
     * @method get
     * @return Post \App\Mock\Schemas\User\UserList[paginate]
     */
    public function index();

    /**
     * @route api/user/{id}
     * @method get
     * @return Post \App\Mock\Schemas\User\ShowUser
     */
    public function show(int $id);
    
    /**
     * @route api/user
     * @method post
     * @return Post \App\Mock\Schemas\User\CreateUser
     */
    public function store(Request $request);

    /**
     * @route api/user/{id}
     * @method put
     * @return Post \App\Mock\Schemas\User\UpdateUser
     */
    public function update(int $id, Request $request);

    /**
     * @route api/user/{id}
     * @method delete
     * @return Post \App\Mock\Schemas\User\DeleteUser
     */
    public function destroy(int $id);
    
}
```









