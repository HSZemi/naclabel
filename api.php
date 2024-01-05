<?php
const SECRET = '0000000000111111111122222222223333333333444444444455555555556666';
const VALIDITY_PERIOD = 5 * 60;


function get_time_tokens(): array {
    $now = time();
    $seed_time_now = $now - ($now % VALIDITY_PERIOD);
    $seed_time_previous = $seed_time_now - VALIDITY_PERIOD;
    $seed_now = hash('sha256', SECRET . $seed_time_now, true);
    $seed_previous = hash('sha256', SECRET . ($seed_time_previous), true);
    $generator_now = new Random\Engine\Xoshiro256StarStar($seed_now);
    $generator_previous = new Random\Engine\Xoshiro256StarStar($seed_previous);
    $token_now = bin2hex($generator_now->generate());
    $token_previous = bin2hex($generator_previous->generate());
    return [$token_previous, $token_now];
}

function is_valid_time_token($timetoken): bool {
    $time_tokens = get_time_tokens();
    return in_array($timetoken, $time_tokens);
}

function get_random_token(): string {
    $bytes = random_bytes(32);
    return bin2hex($bytes);
}

function get_token($timetoken): void {
    if (is_valid_time_token($timetoken)) {
        $random_token = get_random_token();
        touch('data/' . $random_token);
        echo '{"token":"' . $random_token . '"}';
    }
}

function save_pdf($token, $content): void {
    $target_file = 'data/' . $token;
    if (preg_match('/[a-z0-9]+/', $token) && file_exists($target_file)) {
        file_put_contents($target_file, $content);
        echo '{"result":"ok"}';
    }
}

function get_filenames(): void {
    $result = scandir('data');
    if ($result === false) {
        echo json_encode(array("files" => []));
    } else {
        $actual_result = [];
        foreach ($result as $item) {
            if (!str_starts_with($item, '.')) {
                $actual_result[] = $item;
            }
        }
        echo json_encode(array("files" => $actual_result));
    }
}

function get_pdfs($secret): void {
    if ($secret === SECRET) {
        get_filenames();
    }
}

function delete($secret, $token): void {
    if ($secret === SECRET) {
        $target_file = 'data/' . $token;
        if (preg_match('/[a-z0-9]+/', $token) && file_exists($target_file)) {
            unlink($target_file);
            echo '{"result":"ok"}';
        }
    }
}

function get_time_token($secret): void {
    if ($secret === SECRET) {
        $tokens = get_time_tokens();
        echo json_encode(array("timetoken" => $tokens[1]));
    }
}

header('Content-type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
if (isset($_GET['gettoken']) && isset($data['timetoken'])) {
    get_token($data['timetoken']);
} elseif (isset($_GET['savepdf']) && isset($data['token']) && isset($data['content'])) {
    save_pdf($data['token'], $data['content']);
} elseif (isset($_GET['getpdfs']) && isset($data['secret'])) {
    get_pdfs($data['secret']);
} elseif (isset($_GET['delete']) && isset($data['secret']) && isset($data['token'])) {
    delete($data['secret'], $data['token']);
} elseif (isset($_GET['gettimetoken']) && isset($data['secret'])) {
    get_time_token($data['secret']);
} else {
    echo '{"result":"error","message":"Invalid operation"}';
}
