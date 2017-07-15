# Open States Laravel Package

## Installation
From your root directory:

`mkdir packages && cd packages`

`mkdir tkane && cd tkane`

`mkdir open-states && cd open-states`

From the open-states directory, clone this repo:

```sh
git clone git@github.com:thomasjohnkane/open-states-api-laravel-package.git
```


## Configuration

Add you open-states key to your .env file as `OPEN_STATES_KEY`

### composer.json

Edit `composer.json` to reflect the package information.  At a minimum, you need to autoload the package and require guzzle.

```json
{
    "guzzlehttp/guzzle": "^6.2"
    ...
    "autoload": {
        "psr-4": {
            "Tkane\\OpenStates\\": "packages/tkane/open-states/src"
        }
    },
    ...
},
```


### Add the service provider and facade to config/app.php
`Tkane\OpenStates\ServiceProvider::class,`
'OpenStates' => Tkane\OpenStates\Facade::class,
``

### Available Methods

NOTE: http://docs.openstates.org/en/latest/api/

#### Get All Bills By State (paginated)
```
OpenStates::getBills('', [
                        'state' => $state,
                        'search_window' => 'session',
                        'per_page' => 1000,
                        'page' => $i
                    ]);
```
Note: The page should increment until no bills are returned. This is because most states have too many bills for a single call.

#### Get A Bill's Details By Open State ID
```
OpenStates::getBill($bill->os_id);
```
Note: The $os_id is returned in the bill search query as the ID

#### Get All Committees By State (paginated)
```
OpenStates::getCommittees($state);
```

#### Get A Committees's Details By Open State ID
```
OpenStates::getCommittee($committee->os_id);
```
Note: The $os_id is returned in the committee search query as the ID

#### Get A Legislators by State
```
OpenStates::getLegislators($state);
```

## TODO:
1. Add publishable config that uses services.php
2. Add more detail about how entities relate (committee members, legislators, etc)
3. Show more examples of how to use


