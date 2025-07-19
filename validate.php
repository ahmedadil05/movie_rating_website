<?php
require 'db_connect.php'; // Include the database connection

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$field = $data['field'] ?? null;
$value = $data['value'] ?? null;

$response = ['valid' => false, 'message' => 'Invalid input.'];

if ($field && $value) {
    switch ($field) {
        case 'username':
            $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
            $stmt->bind_param("s", $value);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $response['message'] = 'Username already taken.';
            } else {
                $response['valid'] = true;
                $response['message'] = 'Username is available.';
            }
            break;

        case 'email':
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Invalid email format.';
            } else {
                $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
                $stmt->bind_param("s", $value);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $response['message'] = 'Email already in use.';
                } else {
                    $response['valid'] = true;
                    $response['message'] = 'Email is available.';
                }
            }
            break;

        case 'password':
            // Check password requirements
            if (strlen($value) < 8) {
                $response['message'] = 'Password must be at least 8 characters.';
            } elseif (!preg_match('/[A-Z]/', $value)) {
                $response['message'] = 'Password must include at least one uppercase letter.';
            } elseif (!preg_match('/[a-z]/', $value)) {
                $response['message'] = 'Password must include at least one lowercase letter.';
            } elseif (!preg_match('/\d/', $value)) {
                $response['message'] = 'Password must include at least one number.';
            } else {
                $response['valid'] = true;
                $response['message'] = 'Password is strong.';
            }
            break;

        default:
            $response['message'] = 'Invalid field.';
    }
}

echo json_encode($response);
$conn->close();
