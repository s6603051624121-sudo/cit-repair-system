<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'technician') {
    die("Access Denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    $ticket_id = $_POST['ticket_id'];
    $action = $_POST['action'];
    $tech_id = $_SESSION['user_id'];

    try {
        if ($action == 'accept') {
            $stmt = $pdo->prepare("UPDATE tickets SET technician_id = ?, status = 'fixing' WHERE id = ?");
            $stmt->execute([$tech_id, $ticket_id]);
        
        } elseif ($action == 'wait_material') {
            // NO IMAGE CHECK HERE
            $stmt = $pdo->prepare("UPDATE tickets SET status = 'waiting_material' WHERE id = ? AND technician_id = ?");
            $stmt->execute([$ticket_id, $tech_id]);

        } elseif ($action == 'resume') {
             // NO IMAGE CHECK HERE
            $stmt = $pdo->prepare("UPDATE tickets SET status = 'fixing' WHERE id = ? AND technician_id = ?");
            $stmt->execute([$ticket_id, $tech_id]);

        } elseif ($action == 'complete') {
            // ONLY REQUIRE IMAGE HERE
            if (!empty($_FILES['proof_img']['name'])) {
                $newFileName = "proof_" . $ticket_id . "_" . time() . "_" . $_FILES['proof_img']['name'];
                move_uploaded_file($_FILES['proof_img']['tmp_name'], "assets/uploads/" . $newFileName);
                
                $stmt = $pdo->prepare("UPDATE tickets SET status = 'complete', tech_proof_image = ?, updated_at = NOW() WHERE id = ? AND technician_id = ?");
                $stmt->execute([$newFileName, $ticket_id, $tech_id]);
            } else {
                $_SESSION['error'] = "Proof image is required to complete a job.";
            }
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
header("Location: job_pool.php");
exit;
?>