# MONRESTAPI CORE

A pretty library to help developers build `RESTful APIs` lightly.

It's always easy to customize to suit any need such as defining data relationships, authorization, caching, communicating or integrating into other systems.

## Features

* Serves RESTful APIs for any MySql database
  * Pagination 
  * Sorting
  * Selection
  * Grouping, Having
  * Filtering
  * Relationships
  * Metadata
* Supports Event Bus

## Installation

MONRESTAPI CORE is packed as a composer package. So it's installed quickly in 2 steps
1. Require the composer package

    `composer require iamtrungbui/monrestapi`

2. Register the provider: 

    `Iamtrungbui\Monrestapi\MonrestapiServiceProvider`

## System requirements
 - PHP: >= 7.0
 - Laravel/ Lumen Framework: 5.6.*
 - MySQL
 - Message queue server: optional

## API Overview

| HTTP Method | API URL                          | Description           |   For example
|-------------|----------------------------------|-----------------------|--------------------------------- 
| GET         | `/api/entity`                      | List all records of table that match the query                 |   `curl http://deadpool-api.com/api/user?filters=age>20` |
| GET         | `/api/entity/:id`                  | Retrieve a record by primary key :id                      |   `curl http://deadpool-api.com/api/user/123` |
| POST        | `/api/entity`                      | Insert a new record, bulk inserting is also avaiable      |    `curl -X POST http://deadpool-api.com/api/user -d '[{"username":"user1", "age":"20"},{"username":"user2", "age":"25"}]' -H "Content-Type: application/json"` |
| PUT         | `/api/entity/:id`                  | Replaces existed record with new one                      | `curl -X PUT http://deadpool-api.com/api/user/123 -d '{"id":"123", "username":"user1", "age":"20"}' -H "Content-Type: application/json"` |
| PATCH       | `/api/entity/:id`                  | Update record element by primary key                      |  `curl -X PATCH http://deadpool-api.com/api/user/123 -d '{"age":"21"}' -H "Content-Type: application/json"` |
| DELETE      | `/api/entity/:id`                  | Delete a record by primary key                            |  `curl -X DELETE http://deadpool-api.com/api/user/123` |
| DELETE      | `/api/entity`                      | Delete bulk records that match the query                   |  `curl -X DELETE http://deadpool-api.com/api/user?filters=age>100` |
| POST      | `/api/upload`                        | Upload a file                                            |  `curl -X POST http://deadpool-api.com/api/upload -H "Content-Type: multipart/form-data" -F "data=@deadpool.mp3"`

## Pagination

| Parameter   | Required    | Default    | Description                                                      |
|-------------|-------------|------------|------------------------------------------------------------------|
| page_id     | No          | 0          | Page index, start at 0
| page_size   | No          | 50         | Number of rows to retrieve per page

```
/api/post?page_id=2&page_size=20
```

## Sorting

Order by multiple columns using **`sorts`** parameter

### Sort ascending

```
/api/post?sorts=user_id
```

### Sort descending

```
/api/post?sorts=-created_at
```

### Sort by multiple columns

```
/api/post?sorts=user_id,-created_at
```

## Selection

Select columns from the records using **`fields`** parameter. SQL aggregate functions such as `COUNT`, `MAX`, `MIN`, `SUM`, `AVG`, SQL aliases are also available

```
/api/post?fields=id,content,user_id,sum(view_count) as view_sum
```

## Group By

Group the result-set by one or more columns using **`groups`** parameter and combine with aggregate functions using `Selection`

```
/api/post?fields=user_id,sum(view_count)&groups=user_id
```

## Filtering

| Operator     | Condition          |  For example                                         
|--------------|--------------------|----------------------------------
| =            |  EQUAL TO          | /api/deadpool?filters=skill_id=1
| !=           |  NOT EQUAL         | /api/deadpool?filters=skill_id!=1
| >            |  GREATER           | /api/deadpool?filters=skill_id>1
| >=           |  GREATER OR EQUAL  | /api/deadpool?filters=skill_id>=1
| <            |  LESS              | /api/deadpool?filters=skill_id<1
| <=           |  LESS OR EQUAL     | /api/deadpool?filters=skill_id<=1
| ={}          |  IN                | /api/deadpool?filters=skill_id={1;2;3}
| !={}         |  NOT IN            | /api/deadpool?filters=skill_id!={1;2;3}
| =[]          |  BETWEEN           | /api/deadpool?filters=skill_id=[1;20]
| !=[]         |  NOT BETWEEN       | /api/deadpool?filters=skill_id!=[1;20]
| ~            |  LIKE              | /api/deadpool?filters=title~hello
| !~           |  NOTLINE          | /api/deadpool?filters=title!~hello

MONRESTAPI CORE supports filtering records based on more than one `AND`, `NOT` condition by using comma. For example: 

```
/api/post?filters=user_id=1,status={enabled;pending},tile~hello,view_count!=null
```

Complex conditions that combine `AND`, `OR` and `NOT` will be available soon.

## Entity conventions

MONRESTAPI CORE works by a simple mechanism, looking for a model class that correspond to the API entity, otherwise the API entity will be matched to a suitable DB table. That means no model class is required to create, do it only in the case of defining relationships, customizing.

So API entity name should follow one of the conventions:

* The API entity name is the same as a model class name

* Or the API entity name in `snake_case` that correspond to a model class with the name in `CamelCase`

* Or the API entity name is the same as a DB table name

## Relationships

MONRESTAPI CORE is packed into a `Laravel`/ `Lumen` package so relationships also are defined as methods on `Eloquent` model classes.

See Laravel docs for details: https://laravel.com/docs/5.6/eloquent-relationships

Let's consider the following relationship definations:

- A `Nation` has many `City` (one-to-many relationship)

```php
namespace App\Models;
class Nation extends Iamtrungbui\Monrestapi\BaseModel {
    protected $table = 'location_nation';
    public function cities() {
        return $this->hasMany('App\Models\City', 'nation_id', id);
    }
}
```

- A `City` belongs to a `Nation` (many-to-one relationship)
- A `City` has many `District` (one-to-many relationship)

```php
namespace App\Models;
class City extends Iamtrungbui\Monrestapi\BaseModel {
    protected $table = 'location_city';
    public function nation() {
        return $this->belongsTo('App\Models\Nation', 'nation_id');
    }
    public function districts() {
        return $this->hasMany('App\Models\District', 'city_id', id);
    }
}
```

- A `District` belongs to a `City` (many-to-one relationship)

```php
namespace App\Models;
class District extends Iamtrungbui\Monrestapi\BaseModel {
    protected $table = 'location_district';
    public function city() {
        return $this->belongsTo('App\Models\City', 'city_id');
    }
}    
```

### Selection on relationships
MONRESTAPI CORE provides the ability to embed relational data into the results using `embeds` parameter

For example

```
/api/nation?embeds=cities
```

```
/api/city?embeds=nation,districts
```

```
/api/district?embeds=city
```

Even nested relationships

```
/api/nation?embeds=cities.districts
```

```
/api/district?embeds=city.nation
```

### Filtering on relationships

```
/api/city?filters=nation.location_code=EU,districts.name~land
```

## Metric

### metric=get (by default): Retrieve all records that match the query

```
/api/post
```
or

```
/api/post?metric=get
```

Response format

```json
{
    "meta": {
        "has_next": true,
        "total_count": 69,
        "page_count": 2,
        "page_size": 21,
        "page_id": 0
    },
    "result": [],
    "status": "successful"
}
```

### metric=first: Retrieve the first record that matchs the query


```
/api/post?metric=first
```

Response format

```json
{    
    "result": {},
    "status": "successful"
}
```

### metric=count: Retrieve the number of records that match the query

```
/api/post?metric=count
```

Response format

```json
{    
    "result": 69,
    "status": "successful"
}
```

### metric=increment/ decrement: Provides convenient methods for incrementing or decrementing the value of a selected column

```
/api/post?metric=increment&fields=view_count
```

Response format

```json
{    
    "result": 1,
    "status": "successful"
}
```

## Event Bus

... loading 

## .env configurations

| Key | Default value                          | Description                                            |
|-------------|----------------------------------|--------------------------------------------------------- 
|MONRESTAPI_PREFIX_URL         | `api`                  | API URL prefix                                 | 
|MONRESTAPI_MODEL_NAMESPACE         | `App\Models`                  | Models namespace                                 | 
|MONRESTAPI_UPLOAD_PATH         | `/home/upload`                  | Upload path                                 | 
|MONRESTAPI_MQ_ENABLE         | `false`                  | Enable / Disable Message queue (Event Bus)                                | 
|MONRESTAPI_MQ_HOST         |                  | Message queue server host                                 | 
|MONRESTAPI_MQ_PORT         |                   | Message queue server port                                 | 
|MONRESTAPI_MQ_USERNAME         |                   | Message queue authentication - username                                | 
|MONRESTAPI_MQ_PASSWORD         |                   | Message queue authentication - password                                 |                                 | 
| MONRESTAPI__MQ_EXCHANGE        |                   |                                 |                                 | 

##iamtrungbui