## Usage 

First, create your repository class. Note that your repository class MUSR extend `ActivismeBE\DatabaseLayering\Eloquent\Repository` and implement `model()` method:

```php 
<?php 

namespace App\Repositories; 

use App\Films;
use ActivismeBE\DatabaseLayering\{Contracts\RepositoryInterface, Eloquent\Repository};


class FilmsRepository extends Repository 
{
    public function model() : Films
    {
        return Films::class
    }
}
```

By implementing `model()` method you telling repository what model class you waant to use. Now, create `App\Films` model with the following command. `php artisan make:model Films --migration`. 

Then you should have the following code for your database model. 

```php 
<?php

namespace App; 

use Illuminate\Database\Eloquent\Model; 

class Films extends Model 
{
    // 
}
```

And finally, use the repository in the controller: 

```php 
<?php 

namespace App\Repositories\FilmsRepository; 

class FilmsController extends Controller 
{
    private $dbFilms;

    public function __construct(FilmsRepository $dbFilms) 
    {
        $this->dbFilms = $film;
    }

    public function index() 
    {
        return response()->json($this->dbFilms->all());
    }
}
```

## Available Methods 

The following methods are available: 

**ActivismeBE\DatabaseLayering\Contracts\RepositoryInterface**

```php 
public function all($columns = ['*'])
public function lists($value, $key = null)
public function paginate($perPage = 1, $columns = ['*']);
public function create(array $data)

// If you use mongodb then you'll need to specify primary key $attribute
public function update(array $data, $id, $attribute = "id")
public function delete($id)
public function find($id, $columns = ['*'])
public function findBy($field, $value, $columns = ['*'])
public function findAllBy($field, $value, $columns = ['*'])
public function findWhere($where, $columns = ['*'])
public function whereIn($attribute, array $values, $columns = ['*'])
```

**ActivismeBE\DatabaseLayering\Contracts\CriteriaInterface**

```php
public function apply($model, Repository $repository)
```

### Example Usage: 
Create a new film in the repository: 

```php
$this->film->create($input->all());
```

Update existing film: 

```php
$this->film->update($input->all(), $filmId); 
```

Delete film: 

```php 
$this->film->delete($filmId); 
```

Find film by id: 

```php 
$this->film->find($filmId); 
```

you an also choose what columns to fetch: 

```php 
$this->film->find($filmId, ['title', 'description', 'release']); 
```

Get a single row by a single column criteria: 

```php
$this->film->findBy('title', $title);
```

Or you can get all rows by a single column criteria: 

```php 
$this->film->findAllBy('author_id', $authorId);
```

Get all results by multiple fields

```php
$this->film->findWhere(['author_id' => $authorId, ['year', '>', $year]]);
```

## Criteria 

Criteria is a simple way to apply specific condition, or set conditions to the repository query.
Your criteria class MUST extends the abstract `ActivismeBE\DatabaseLayering\Repositories\Criteria\Criteria` class. 

Here is a simple criteria: 

```php 
<?php 

namespace App\Repositories\Criteria\Films; 

use ActivismeBE\DatabaseLayering\{Criteria\Criteria, Contracts\RepositoryInterface};

class LengthOverTwoHours extends Criteria 
{
    /**
     * @param mixed               $modem
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('lenght', '>', 120);
    }
}
```

Now, Inside your controller class you call the pushCriteria method: 

```php 
<?php

namespace App\Http\Controllers; 

use App\Repositories\{FilmsRepository, Criteria\Films\LenghtOverTwoHours};

class FilmsController extends Controller 
{
    private $film; 

    public function __construct(FilmsRepository $film) 
    {
        $this->film = $film;
    }

    public function index() 
    {
        $this->film->pushCriteria(new LengthOverTwoHours()); 
        return response()->json($this->film()->all());
    }
}
```