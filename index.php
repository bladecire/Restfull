<?php
function autoload_class($class_name) {
    $directories = array('classes/','classes/controllers/','classes/models/');
    foreach ($directories as $directory) {
        $filename = $directory . $class_name . '.php';
        if (is_file($filename)) {require($filename);break;}
    }
}

spl_autoload_register('autoload_class');

$request = new Request();
if (isset($_SERVER['PATH_INFO'])) {
    $request->url_elements = explode('/', trim($_SERVER['PATH_INFO'], '/'));
}
$request->method = strtoupper($_SERVER['REQUEST_METHOD']);
switch ($request->method) {
    case 'GET':
        $request->parameters = $_GET;
    break;
    case 'POST':
        $request->parameters = $_POST;
    break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $request->parameters);
    break;
    case 'DELETE':
        parse_str(file_get_contents('php://input'), $request->parameters);
    break;
}

if (!empty($request->url_elements) && class_exists('UserController')) {
        $controller = new UserController;
        $request->action= strtolower($request->url_elements[0]);
        $action_name = strtolower($request->action);
        $response_str = call_user_func_array(array($controller, $action_name), array($request));
}
else {
    header('HTTP/1.1 404 Not Found');
    $response_str = 'Petició desconeguda: ' . $request->url_elements[0];
}

$response_obj = Response::create($response_str, $_SERVER['HTTP_ACCEPT']);
echo $response_obj->render();