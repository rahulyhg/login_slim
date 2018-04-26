<?php

use Slim\Http\Request;
use Slim\Http\Response;

use \App\Models\User;

$container['db'];

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/teste/', function (Request $request, Response $response, array $args) {
    $table = $this->db->table('users');
    $users = $table->get();
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'users/index.phtml', ['users'=>$users]);
});

//Login
$app->map(['get','post'],'/login[/[{id}]]',function(Request $request, Response $response, array $args){
    if(isset($args['id'])){
        $status = $args['id'];
    }
    return $this->renderer->render($response, 'login/index.phtml', ['status'=>$status]);
});

//Authentication
$app->post('/login/auth/',function(Request $request,Response $response, array $args){
    $data = $request->getParsedBody();
    $user = User::where('email','=',$data['email'])->where('password','=',$data['password'])->first();
    if(is_null($user)){
        return $response->withStatus(302)->withHeader('Location','/login/0');
    }else{
        session_start();
        $_SESSION['user'] = $user->name;
        return $response->withStatus(302)->withHeader('Location','/admin');
    }
});

//Dashboard
$app->get('/admin',function(Request $request, Response $response, array  $args){
    session_start();
    if(isset($_SESSION['user'])){
        return $this->renderer->render($response, 'admin/dashboard.phtml');
    }else{
        return $response->withStatus(302)->withHeader('Location','/login');
    }
});

//Logout

$app->get('/logout', function(Request $request, Response $response, array  $args){
    unset($_SESSION['user']);
    return $response->withStatus(302)->withHeader('Location','/login');
});

//Register
$app->get('/register[/[{id}]]',function (Request $request, Response $response, array  $args){
    if(isset($args['id'])){
            $status = $args['id'];
    }
   return $this->renderer->render($response, 'login/register.phtml',['status'=>$status]);
});

$app->post('/register',function(Request $request,Response $response, array  $args){
    $data = $request->getParsedBody();

    if(is_null(User::where('email','=',$data['email'])->first())){
        $user = User::create($data);
        return $response->withStatus(302)->withHeader('Location','/login/1');
    }else{
        return $response->withStatus(302)->withHeader('Location','/register/0');
    }
});
