<?php
include 'db/db_con.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <?php 
    include("navbar.php");
    ?>
</head>
<body>
<div class="container mt-5 ml-5 mr-5">
    <h2 class="mb-4">Register</h2>
    <form id="regForm" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name <span style="color: red;">*</span></label>
            <input type="text" class="form-control border border-dark shadow-sm" id="first_name" name="first_name">
            <div id="nameError" class="text-danger mt-2"style="color: red; display: none;"></div>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name <span style="color: red;">*</span></label>
            <input type="text" class="form-control border border-dark shadow-sm" id="last_name" name="last_name">       
        </div>
        <div class="mb-3">
            <label for="middle_name" class="form-label">Middle Name</label>
            <input type="text" class="form-control border border-dark shadow-sm" id="middle_name" name="middle_name">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email <span style="color: red;">*</span></label>
            <input type="email" class="form-control border border-dark shadow-sm" id="email" name="email">
            <div id="emailError" class="text-danger mt-2"style="color: red; display: none;"></div>
        </div>
        <div class="mb-3">
            <label for="number" class="form-label">Phone Number <span style="color: red;">*</span></label>
            <input type="number" class="form-control border border-dark shadow-sm" id="number" name="number">
            <div id="numberError" class="text-danger mt-2"style="color: red; display: none;"></div>
        </div>
        <div class="mb-3">
            <label for="picture" class="form-label">Profile Picture Upload <span style="color: red;">*</span></label>
            <input type="file" class="form-control border border-dark shadow-sm" id="picture" name="picture">
            <div id="pictureError" class="text-danger mt-2"style="color: red; display: none;"></div>
        </div>
        <input type="button" class="btn btn-primary" onclick="save_user()" value="Register">
    </form>
</div>
<script type="text/javascript">

    function clearError(element) {
        if (element) {
            element.hide();
            element.text('');
        }
    }


    function isValidEmail(email) {
        const xpattern= /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return xpattern.test(email);
    }

    function isValidPicture() {
        const xpictureInput = $('#picture').get(0);
        const xpictureError = $('#pictureError');
        const xfile = xpictureInput.files[0];
        const xallowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        clearError(xpictureError);

        if (xfile) {
            
            if (!xallowedTypes.includes(xfile.type)) {
            xpictureError.text('Please upload a valid image (jpg, png, gif).');
            xpictureError.show();
            return false;
            }   

            const maxSizeInMB = 2;
            if (xfile.size > maxSizeInMB * 1024 * 1024) {
            xpictureError.text('File size must not exceed 2MB.');
            xpictureError.show();
            return false;
            }
        }

        return true;
    }

  

    function save_user() {
        var xfirstName = $('#first_name').val().trim();
        var xlastName = $('#last_name').val().trim();
        var xemail = $('#email').val().trim();
        var xnumber = $('#number').val().trim();
        var xpicture = $('#picture').val();
        const xnumberError = $('#numberError');
        const xemailError = $('#emailError');
        const xnameError = $('#nameError');
 

        if (!xfirstName || !xlastName || !xemail || !xnumber || !xpicture) {
            alertify.alert("Please fill out all required fields.");
            return;
        }

        if (!isValidEmail(xemail)) {
            alertify.alert("Please enter a valid email address.");
            return;
        }

        if (!/^\d{11}$/.test(xnumber)) {
            alertify.alert("Please enter a valid phone number with exactly 11 digits.");
            return;
        }

        if (!isValidPicture()) {
            return;
        }

        $.ajax({
        url: "./func/func_register.php",
        type: "POST",
        dataType: "json",
        data: {
            email: xemail,
            number: xnumber,
            first_name: xfirstName,
            last_name: xlastName,
            event_action: "check_exist",
        },
        success: function(response) {
            let message = "";
            let valid = true;
            clearError(xnumberError);
            clearError(xemailError);
            clearError(xnameError);

            if (response.email_exists) {
                xemailError.text('The provided email already exists.');
                xemailError.show();
                valid = false; 
            }
            if (response.number_exists) {
                xnumberError.text('The provided phone number already exists.');
                xnumberError.show();
                valid = false;
            }
            if (response.name_exists) {
                xnameError.text("The combination of first name and last name already exists.");
                xnameError.show();
                valid = false;
            }


            if (!valid) {
                return; 
            }


            var formData = new FormData($('#regForm')[0]);
            formData.append('event_action', 'save_user');
            
            $.ajax({
                url: "./func/func_register.php",
                type: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                success: function(xres) {
                    if (xres.success) {
                        $('body').append(xres.msg);
                        $('#regInfo').html(`
                            <strong>First Name:</strong> ${xres.first_name}<br>
                            <strong>Last Name:</strong> ${xres.last_name}<br>
                            <strong>Middle Name:</strong> ${xres.middle_name}<br>
                            <strong>Email:</strong> ${xres.email}<br>
                            <strong>Phone Number:</strong> ${xres.phone_number}<br>
                            <strong>Profile Picture:</strong> <img src="uploads/${xres.profile_image}" alt="Profile Picture" class="img-thumbnail" width="100">
                        `);
                        $('#regInfoModal').modal('show').on('hidden.bs.modal', function () {
                            location.reload();
                        });
                    } else {
                        alertify.alert(xres.msg);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Error: " + xhr.responseText);
                }
            });
        },
        error: function(xhr, status, error) {
            alert("Error: " + xhr.responseText);
        }
    });
    }
</script>
</body>
</html>
