<?php

use Phalcon\Mvc\Micro;
use Phalcon\Db\Adapter\Pdo\Mysql as MysqlAdapter;

$loader = new \Phalcon\Loader();

$loader->registerDirs(
    [
        __DIR__ . '/models/',
    ]
)->register();

$app = new Micro();

// Setup the database service with events manager
$app['db'] = function () {
    $eventsManager = new EventsManager();
    $eventsManager->attach(
        'db:beforeQuery',
        function (Event $event, $connection) {
                $sql = $connection->getSQLStatement();
                echo "<pre style='background-color: lightgrey; padding: 5 0;'>SQL statement:  $sql</pre>";
            }
        );

    $connection = new MysqlAdapter(
        [
            'host'     => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbname'   => 'demo',
        ]
    );

    $connection->setEventsManager($eventsManager);

    return $connection;
};

$app->get(
    "/",
    function () {
        echo "<h1>Welcome</h1>";
    }
);

$app->get(
    "/search",
    function () use ($app) {
        $users = $app['db']->query("SELECT id, first_name, last_name FROM users");

        while ($user = $users->fetch()) {
            echo $user['first_name'] . "<br>";
         }
    }
);

$app->get(
    "/show/{id:[0-9]}",
    function ($id) use ($app) {
        $user = $users::findFirst($id);
        echo $user->first_name . "<br>";  // echo $user->firstName will return empty
    }
);

// Only works with Phalcon 3.x
$app->get(
    "/saveFindFirst/{name}",
    function ($name) use ($app) {
        $john = Users::findFirst(1);
        echo "Current name: " . $john->first_name . 
        " will be changed to $name<br>";

        $post = [
            'id' => 1,
            'first_name' => $name
        ];
        
        $john->update($post);
        
        $newJohn = Users::findFirst(1);
        echo "New name: " . $newJohn->first_name;                      
    }
);

$app->get(
    "/saveModel/{name}",
    function($name) use ($app) {

        $john = Users::findFirst(1);
        echo "Current name: " . $john->first_name . 
        " will be changed to $name<br>";

        $post = [
            'id' => 1,
            'first_name' => $name
        ];

        $user = new Users();

        $user->assign($post);
        
        $user->save();
        
        $john = Users::findFirst(1);
        
        echo "New name: " . $john->first_name;  
    }
);

$app->get(
    "/updateModel/{name}",
    function($name) use ($app) {

        $john = Users::findFirst(1);
        echo "Current name: " . $john->first_name . 
        " will be changed to $name<br>";

        $post = [
            'id' => 1,
            'first_name' => $name
        ];

        $user = new Users();

        $user->assign($post);
        
        $user->update();

        $john = Users::findFirst(1);
        
        echo "New name: " . $john->first_name;            
        }
    }
);

$app->get(
    "/updateModelFindFirst/{name}",
    function($name) use ($app) {

        $john = Users::findFirst(1);
        echo "Current name: " . $john->first_name . 
        " will be changed to $name<br>";

        $john->assign(
            [
                'first_name' => $name
            ],
            [
                'first_name',
                'last_name'
            ]
         );
        
        $john->update();

        $newJohn = Users::findFirst(1);
            
        echo "New name: " . $newJohn->first_name; 
        
        }
    }
);

$app->get(
    "/updateModelWhitelist/{name}",
    function($name) use ($app) {

        $john = Users::findFirst(1);
        echo "Current name: " . $john->first_name . 
        " will be changed to $name<br>";

        $post = [
            'id' => 1,
            'first_name' => $name
        ];

        $user = new Users();

        $user->assign($post,
            [
                'first_name',
                'last_name'
            ]
         );
        
        $user->update();
        
        $john = Users::findFirst(1);
        
        echo "New name: " . $john->first_name;   

    }
);

$app->get(
    "/updateRaw/{name}",
    function($name) use ($app) {
        $app['db']->query("UPDATE users SET first_name = '$name' WHERE id = 1");
        $johhnyRaw = Users::findFirst(1);
        echo $johhnyRaw->first_name;
    }
);

$app->handle($_SERVER["REQUEST_URI"]);
