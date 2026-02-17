<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data) || empty($data['action'])) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

$actionCalled = $data['action'];
$conn = db_connect();

function fail_json($message) {
    echo json_encode(["success" => false, "message" => $message]);
    exit();
}

function prepare_or_fail($conn, $sql) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        fail_json("Database error: " . $conn->error);
    }
    return $stmt;
}

function fetch_reviews($conn, $motorcycleId) {
    $reviews = [];
    $stmt = prepare_or_fail(
        $conn,
        "SELECT r.rating, r.review_text, r.created_at, u.username
         FROM motorcycle_reviews r
         INNER JOIN users u ON r.user_id = u.id
         WHERE r.motorcycle_id = ?
         ORDER BY r.created_at DESC"
    );
    $stmt->bind_param("i", $motorcycleId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();

    $summary = ["average" => null, "total" => 0];
    $summaryStmt = prepare_or_fail(
        $conn,
        "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews
         FROM motorcycle_reviews
         WHERE motorcycle_id = ?"
    );
    $summaryStmt->bind_param("i", $motorcycleId);
    $summaryStmt->execute();
    $summaryResult = $summaryStmt->get_result();
    if ($summaryRow = $summaryResult->fetch_assoc()) {
        $summary["average"] = $summaryRow["avg_rating"] !== null ? round((float)$summaryRow["avg_rating"], 1) : null;
        $summary["total"] = (int)$summaryRow["total_reviews"];
    }
    $summaryStmt->close();

    return ["reviews" => $reviews, "summary" => $summary];
}

function review_gate($conn, $motorcycleId, $userId, $roleId) {
    if (!$userId) {
        return ["canReview" => false, "message" => "Log in to leave a review."];
    }

    $existingStmt = prepare_or_fail(
        $conn,
        "SELECT id FROM motorcycle_reviews WHERE user_id = ? AND motorcycle_id = ? LIMIT 1"
    );
    $existingStmt->bind_param("ii", $userId, $motorcycleId);
    $existingStmt->execute();
    $existingResult = $existingStmt->get_result();
    $hasReviewed = $existingResult->num_rows > 0;
    $existingStmt->close();

    if ($hasReviewed) {
        return ["canReview" => false, "message" => "You already reviewed this motorcycle."];
    }

    return ["canReview" => true, "message" => ""];
}

if ($actionCalled == "getMotorcycleDetails") {
    if (empty($data['motorcycleId'])) {
        echo json_encode(["success" => false, "message" => "motorcycleId was not provided."]);
        exit();
    }

    $motorcycleId = (int)$data['motorcycleId'];

    $stmt = prepare_or_fail($conn, "SELECT * FROM motorcycles WHERE id = ?");
    $stmt->bind_param("i", $motorcycleId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $motorcycle = $result->fetch_assoc();
    } else {
        echo json_encode(['success' => false, 'message' => 'No motorcycle found with this ID.']);
        exit;
    }
    $stmt->close();

    $stmt = prepare_or_fail($conn, "SELECT image_path FROM motorcycle_images WHERE motorcycle_id = ? ORDER BY image_order");
    $stmt->bind_param("i", $motorcycleId);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $images = [];
    while ($stmt->fetch()) {
        $images[] = $image_path;
    }
    $stmt->close();

    $reviewsPayload = fetch_reviews($conn, $motorcycleId);
    $userId = $_SESSION['id'] ?? null;
    $roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : null;
    $reviewGate = review_gate($conn, $motorcycleId, $userId, $roleId);

    echo json_encode([
        "success" => true,
        "name" => $motorcycle['name'],
        "pricePerDay" => $motorcycle['price_per_day'],
        "fuel" => $motorcycle['fuel'],
        "engineCc" => $motorcycle['engine_cc'],
        "transmission" => $motorcycle['transmission'],
        "year" => $motorcycle['year'],
        "abs" => $motorcycle['abs'],
        "color" => $motorcycle['color'],
        "type" => $motorcycle['type'],
        "weightKg" => $motorcycle['weight_kg'],
        "seatHeightMm" => $motorcycle['seat_height_mm'],
        "images" => $images,
        "reviews" => $reviewsPayload["reviews"],
        "reviewSummary" => $reviewsPayload["summary"],
        "canReview" => $reviewGate["canReview"],
        "reviewMessage" => $reviewGate["message"]
    ]);
    exit;
} elseif ($actionCalled == "getReviews") {
    if (empty($data['motorcycleId'])) {
        echo json_encode(["success" => false, "message" => "motorcycleId was not provided."]);
        exit();
    }

    $motorcycleId = (int)$data['motorcycleId'];
    $reviewsPayload = fetch_reviews($conn, $motorcycleId);
    $userId = $_SESSION['id'] ?? null;
    $roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : null;
    $reviewGate = review_gate($conn, $motorcycleId, $userId, $roleId);

    echo json_encode([
        "success" => true,
        "reviews" => $reviewsPayload["reviews"],
        "reviewSummary" => $reviewsPayload["summary"],
        "canReview" => $reviewGate["canReview"],
        "reviewMessage" => $reviewGate["message"]
    ]);
    exit;
} elseif ($actionCalled == "addReview") {
    if (!isset($_SESSION['id'])) {
        echo json_encode(["success" => false, "message" => "User is not logged in."]);
        exit();
    }

    if (empty($data['motorcycleId']) || empty($data['rating']) || empty($data['reviewText'])) {
        echo json_encode(["success" => false, "message" => "Missing required information."]);
        exit();
    }

    $motorcycleId = (int)$data['motorcycleId'];
    $rating = (int)$data['rating'];
    $reviewText = trim($data['reviewText']);

    if ($rating < 1 || $rating > 5) {
        echo json_encode(["success" => false, "message" => "Invalid rating."]);
        exit();
    }

    if ($reviewText === "") {
        echo json_encode(["success" => false, "message" => "Review text is required."]);
        exit();
    }

    $userId = (int)$_SESSION['id'];
    $roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : null;
    $reviewGate = review_gate($conn, $motorcycleId, $userId, $roleId);
    if (!$reviewGate["canReview"]) {
        echo json_encode(["success" => false, "message" => $reviewGate["message"]]);
        exit();
    }

    $stmt = prepare_or_fail(
        $conn,
        "INSERT INTO motorcycle_reviews (motorcycle_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("iiis", $motorcycleId, $userId, $rating, $reviewText);

    if ($stmt->execute()) {
        $stmt->close();
        $reviewsPayload = fetch_reviews($conn, $motorcycleId);
        $reviewGate = review_gate($conn, $motorcycleId, $userId, $roleId);
        echo json_encode([
            "success" => true,
            "message" => "Review added.",
            "reviews" => $reviewsPayload["reviews"],
            "reviewSummary" => $reviewsPayload["summary"],
            "canReview" => $reviewGate["canReview"],
            "reviewMessage" => $reviewGate["message"]
        ]);
        exit();
    }

    echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    $stmt->close();
    exit();
} elseif ($actionCalled == "addToCart") {
    if (!isset($_SESSION['id'])) {
        echo json_encode(["success" => false, "message" => "User is not logged in."]);
        exit();
    }

    if (empty($data['motorcycleId']) || empty($data['startDate']) || empty($data['endDate']) || empty($data['totalDays']) || empty($data['pricePerDay'])) {
        echo json_encode(["success" => false, "message" => "Missing required information."]);
        exit();
    }

    $totalPrice = $data['totalDays'] * $data['pricePerDay'];

    $checkStmt = prepare_or_fail(
        $conn,
        "SELECT * FROM motorcycle_bookings
         WHERE motorcycle_id = ?
         AND (
             (status = 'confirmed' AND end_date >= ? AND start_date <= ?)
             OR
             (status = 'pending' AND booking_date >= NOW() - INTERVAL 5 MINUTE AND end_date >= ? AND start_date <= ?)
         )"
    );
    $checkStmt->bind_param(
        "issss",
        $data['motorcycleId'],
        $data['startDate'],
        $data['endDate'],
        $data['startDate'],
        $data['endDate']
    );
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "This vehicle is already reserved for at least one of the selected dates."]);
        $checkStmt->close();
        $conn->close();
        exit();
    }

    $stmt = prepare_or_fail(
        $conn,
        "INSERT INTO motorcycle_cart (user_id, motorcycle_id, start_date, end_date, total_price)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("iissi", $_SESSION['id'], $data['motorcycleId'], $data['startDate'], $data['endDate'], $totalPrice);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Added to cart."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    $checkStmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Unknown action."]);
}

$conn->close();
?>
