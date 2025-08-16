<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "INSERT INTO inquiries (message, email, role)
            VALUES (:message, :email, :role)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        echo "<script>
                alert('Thank you for reaching out! Your inquiry has been received.');
                window.location.href='contact.html';
              </script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
