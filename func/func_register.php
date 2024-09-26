<?php
include '../db/db_con.php';
require_once('../include/JSON.php');


if ($_POST["event_action"] == "check_exist") {
$email = $_POST['email'];
$number = $_POST['number'];
$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
   
// Check if email exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$stmt->execute([$email]);
$xretobj['email_exists'] = $stmt->fetchColumn() > 0;

// Check if phone number exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE phone_number = ?");
$stmt->execute([$number]);
$xretobj['number_exists'] = $stmt->fetchColumn() > 0;

// Check if the combination of first and last name exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE first_name = ? AND last_name = ?");
$stmt->execute([$firstName, $lastName]);
$xretobj['name_exists'] = $stmt->fetchColumn() > 0;
}



if ($_POST["event_action"] == "save_user") {
    $xfirst_name =$_POST['first_name'];
    $xlast_name = $_POST['last_name'];
    $xmiddle_name = $_POST['middle_name'];
    $xemail= $_POST['email'];
    $xnumber = $_POST['number'];
    $xuploadDir = '../uploads/';
    $xcreatedAt = date('Y-m-d H:i:s');

    if (!is_dir($xuploadDir)) {
        mkdir($xuploadDir, 0755, true);
    }


    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $ximage = $_FILES['picture'];
        $xfileName = $xemail . '.'. pathinfo($ximage['name'], PATHINFO_EXTENSION);
        $xfilePath = $xuploadDir . $xfileName;
        $xfileType = strtolower(pathinfo($xfilePath, PATHINFO_EXTENSION));

        if (file_exists($xfilePath)) {
            unlink($xfilePath);
        }

        if (move_uploaded_file($ximage['tmp_name'], $xfilePath)) {
            $stmt = $pdo->prepare("INSERT INTO `users` (`first_name`, `last_name`, `middle_name`, `email`, `phone_number`, `profile_image`, `created_at`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$xfirst_name, $xlast_name, $xmiddle_name, $xemail, $xnumber, $xfilePath, date('Y-m-d H:i:s')]);
        
            $xretobj["success"] = true;
            $xretobj["msg"] = "
            <div class='modal fade' id='regInfoModal' tabindex='-1' aria-labelledby='regInfoModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='regInfoModalLabel'>Registration Successful</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            <p id='regInfo'></p>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                        </div>
                    </div>
                </div>
            </div>";
            $xretobj["first_name"] = $xfirst_name;
            $xretobj["last_name"] = $xlast_name;
            $xretobj["middle_name"] = $xmiddle_name;
            $xretobj["email"] = $xemail;
            $xretobj["phone_number"] = $xnumber;
            $xretobj["profile_image"] = $xfilePath;
                
        } else {
            $xretobj["success"] = false;
            $xretobj["msg"] = "Something went wrong";
        }
        
    } else {
        $xretobj["success"] = false;
        $xretobj["msg"] = "Please upload a profile picture.";
    }
}

$json = new Services_JSON();
echo $json->encode($xretobj);
?>
