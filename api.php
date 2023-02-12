<?php
header('Content-type: application/json');
if (isset($_GET['save'])
    && isset($_POST['username'])
    && isset($_POST['usernameSize'])
    && isset($_POST['topMargin'])
    && isset($_POST['languages'])
    && isset($_POST['tagline'])) {
    $number = rand(10000, 99999);
    $filepath = 'data/' . $number . '.json';
    $success = false;
    for ($i = 0; $i = 10; $i++) {
        if (!file_exists($filepath)) {
            $success = true;
            break;
        } else {
            $number = rand(10000, 99999);
            $filepath = 'data/' . $number . '.json';
        }
    }
    if (!$success) {
        echo '{"result":"error","message":"could not generate number :("}';
        die();
    }
    file_put_contents($filepath, json_encode(array(
        'username' => $_POST['username'],
        'usernameSize' => $_POST['usernameSize'],
        'topMargin' => $_POST['topMargin'],
        'languages' => $_POST['languages'],
        'tagline' => $_POST['tagline']
    )));
    echo '{"result":"ok","message":"' . $number . '"}';
} elseif (isset($_GET['load'])) {
    $number = intval($_GET['load']);
    $filepath = 'data/' . $number . '.json';
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        echo $content;
    } else {
        echo '{"result":"error","message":"No data for number ' . $number . '"}';
    }
} else {
    echo '{"result":"error","message":"Invalid operation"}';
}

?>