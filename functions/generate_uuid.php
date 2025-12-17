<?php

function generate_uuid() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

?>


// Contoh penggunaan:
// echo generate_uuid();
// Output: misalnya "a3f2b1c4-5d6e-4f7a-8b9c-0d1e2f3a4b5c"

// Atau untuk menyimpan ke database:
// $uuid = generate_uuid();
// $query = "INSERT INTO users (id, name) VALUES ('$uuid', 'John Doe')";