<?php
function getUserSaldo($userId, $connection) {
    // Prepare the SQL statement to prevent SQL injection
    $query = "SELECT saldo FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        // Bind the user ID parameter
        mysqli_stmt_bind_param($stmt, "i", $userId);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get the result
        $result = mysqli_stmt_get_result($stmt);

        // Fetch the user's data
        $user = mysqli_fetch_assoc($result);

        // Close the statement
        mysqli_stmt_close($stmt);

        // Return the saldo if user is found, otherwise return 0
        return $user ? $user['saldo'] : 0;
    }

    // Return 0 if the query preparation fails
    return 0;
}