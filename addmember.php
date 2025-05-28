<?php

session_start();

// Define allowed roles
$allowed_roles = ['admin', 'repair'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    
    // Redirect to the login page if not authorized
    header("Location: index.php");
    exit();
}
//database connection
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$ServicePersonId = $Name = $Location = $epf_number = $etf_number = "";
$successMessage = $errorMessage = "";

// Handle INSERT, UPDATE, DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ServicePersonId = $_POST['ServicePersonId'];
    $Name = $_POST['Name'];
    $Location = $_POST['Location'];
    $epf_number = $_POST['epf_number'];
    $etf_number = $_POST['etf_number'];

    // Handle photo upload
    $photo = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/members/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = $ServicePersonId . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $photo;
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['photo']['tmp_name']);
        if($check === false) {
            $errorMessage = "File is not an image.";
        } else {
            // Check file size (limit to 5MB)
            if ($_FILES['photo']['size'] > 5000000) {
                $errorMessage = "Sorry, your file is too large. Maximum size is 5MB.";
            } else {
                // Allow certain file formats
                if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif" ) {
                    $errorMessage = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                } else {
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                        // Photo uploaded successfully
                    } else {
                        $errorMessage = "Sorry, there was an error uploading your file.";
                        $photo = "";
                    }
                }
            }
        }
    }

    if (isset($_POST['add']) && empty($errorMessage)) {
        try {
            $stmt = $conn->prepare("INSERT INTO Members (ServicePersonId, NAME, Location, epf_number, etf_number, photo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $ServicePersonId, $Name, $Location, $epf_number, $etf_number, $photo);
            $stmt->execute();
            $successMessage = "Member added!";
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $errorMessage = "Error: A member with this ID already exists.";
            } else {
                $errorMessage = "There is an error occurred.";
            }
        }
    
    
    
    } elseif (isset($_POST['update']) && empty($errorMessage)) {
        // For update, only update photo if a new one is uploaded
        if (!empty($photo)) {
            // Delete old photo if exists
            $old_photo_stmt = $conn->prepare("SELECT photo FROM Members WHERE ServicePersonId = ?");
            $old_photo_stmt->bind_param("s", $ServicePersonId);
            $old_photo_stmt->execute();
            $old_photo_result = $old_photo_stmt->get_result();
            if ($old_photo_row = $old_photo_result->fetch_assoc()) {
                if (!empty($old_photo_row['photo']) && file_exists("uploads/members/" . $old_photo_row['photo'])) {
                    unlink("uploads/members/" . $old_photo_row['photo']);
                }
            }
            $old_photo_stmt->close();
            
            $stmt = $conn->prepare("UPDATE Members SET NAME = ?, Location = ?, epf_number = ?, etf_number = ?, photo = ? WHERE ServicePersonId = ?");
            $stmt->bind_param("ssssss", $Name, $Location, $epf_number, $etf_number, $photo, $ServicePersonId);
        } else {
            $stmt = $conn->prepare("UPDATE Members SET NAME = ?, Location = ?, epf_number = ?, etf_number = ? WHERE ServicePersonId = ?");
            $stmt->bind_param("sssss", $Name, $Location, $epf_number, $etf_number, $ServicePersonId);
        }
        
        if ($stmt->execute()) $successMessage = "Member updated!";
        else $errorMessage = "Update failed: " . $stmt->error;
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        // Delete photo file before deleting record
        $photo_stmt = $conn->prepare("SELECT photo FROM Members WHERE ServicePersonId = ?");
        $photo_stmt->bind_param("s", $ServicePersonId);
        $photo_stmt->execute();
        $photo_result = $photo_stmt->get_result();
        if ($photo_row = $photo_result->fetch_assoc()) {
            if (!empty($photo_row['photo']) && file_exists("uploads/members/" . $photo_row['photo'])) {
                unlink("uploads/members/" . $photo_row['photo']);
            }
        }
        $photo_stmt->close();
        
        $stmt = $conn->prepare("DELETE FROM Members WHERE ServicePersonId = ?");
        $stmt->bind_param("s", $ServicePersonId);
        if ($stmt->execute()) $successMessage = "Member deleted!";
        else $errorMessage = "Delete failed: " . $stmt->error;
        $stmt->close();
    }
}

// Fetch all members
$members = $conn->query("SELECT * FROM Members ORDER BY NAME");

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Members</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');">

   <!-- Back Button - Top Right Corner -->
   <div class="absolute top-10 right-10 z-50">
  <img 
    src="img/back.png" 
    onclick="history.back()" 
    alt="Back" 
    class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
  />
  </div>

   <!-- Header with Logo and Title in a Blurred Background -->
   <section class="flex items-center justify-center pt-10 px-4">
    <div class="backdrop-blur-md bg-white/20 rounded-2xl shadow-xl p-6 flex items-center gap-6 mb-6">
      <img src="img/logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-red-700 ">BC–Agro Tronics</h1>
        <p class="text-xl text-red-400">Manage Service Team Members</p>
      </div>
    </div>
  </section>

  <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-xl">
    <h1 class="text-3xl font-bold text-red-600 mb-6 text-center">Manage Service Team Members</h1>

    <?php if ($successMessage): ?>
      <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
      <div>
        <label class="block font-semibold text-gray-700">Service Person ID</label>
        <input type="text" name="ServicePersonId" id="ServicePersonId" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Name</label>
        <input type="text" name="Name" id="Name" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Location</label>
        <input type="text" name="Location" id="Location" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">EPF Number</label>
        <input type="text" name="epf_number" id="epf_number" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">ETF Number</label>
        <input type="text" name="etf_number" id="etf_number" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Photo</label>
        <input type="file" name="photo" id="photo" accept="image/*" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        <div id="photoPreview" class="mt-2"></div>
      </div>
      <div class="col-span-1 md:col-span-2 lg:col-span-3 flex gap-4 mt-4">
        <button name="add" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-xl">Add</button>
        <button name="update" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-xl">Update</button>
        <button name="delete" onclick="return confirm('Are you sure you want to delete this member?');" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-xl">Delete</button>
        <button type="button" onclick="clearForm()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-xl">Clear</button>
      </div>
    </form>

    <div class="overflow-x-auto overflow-y-auto max-h-[500px] border rounded-xl shadow-inner">
      <table class="min-w-full table-auto text-sm text-left text-gray-700">
        <thead class="bg-red-200 text-red-800 sticky top-0">
          <tr>
            <th class="px-4 py-2">Photo</th>
            <th class="px-4 py-2">Service Person ID</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Location</th>
            <th class="px-4 py-2">EPF Number</th>
            <th class="px-4 py-2">ETF Number</th>
          </tr>
        </thead>
        <tbody id="memberTable" class="bg-white divide-y divide-gray-200">
          <?php while ($row = $members->fetch_assoc()): ?>
            <tr class="cursor-pointer hover:bg-red-100 transition" onclick="fillForm('<?= $row['ServicePersonId'] ?>', '<?= htmlspecialchars($row['NAME'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['Location'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['epf_number'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['etf_number'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['photo'], ENT_QUOTES) ?>')">
              <td class="px-4 py-2">
                <?php if (!empty($row['photo'])): ?>
                  <img src="uploads/members/<?= $row['photo'] ?>" alt="Photo" class="w-12 h-12 object-cover rounded-full" />
                <?php else: ?>
                  <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                    <span class="text-gray-500 text-xs">No Photo</span>
                  </div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2"><?= $row['ServicePersonId'] ?></td>
              <td class="px-4 py-2"><?= $row['NAME'] ?></td>
              <td class="px-4 py-2"><?= $row['Location'] ?></td>
              <td class="px-4 py-2"><?= $row['epf_number'] ?></td>
              <td class="px-4 py-2"><?= $row['etf_number'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function fillForm(id, name, location, epf, etf, photo) {
      document.getElementById('ServicePersonId').value = id;
      document.getElementById('Name').value = name;
      document.getElementById('Location').value = location;
      document.getElementById('epf_number').value = epf;
      document.getElementById('etf_number').value = etf;
      
      // Show current photo if exists
      const photoPreview = document.getElementById('photoPreview');
      if (photo) {
        photoPreview.innerHTML = `
          <div class="mt-2">
            <p class="text-sm text-gray-600 mb-2">Current Photo:</p>
            <img src="uploads/members/${photo}" alt="Current Photo" class="w-20 h-20 object-cover rounded-lg border" />
          </div>
        `;
      } else {
        photoPreview.innerHTML = '';
      }
    }

    function clearForm() {
      document.getElementById('ServicePersonId').value = '';
      document.getElementById('Name').value = '';
      document.getElementById('Location').value = '';
      document.getElementById('epf_number').value = '';
      document.getElementById('etf_number').value = '';
      document.getElementById('photo').value = '';
      document.getElementById('photoPreview').innerHTML = '';
    }

    // Preview selected image
    document.getElementById('photo').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const preview = document.getElementById('photoPreview');
      
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.innerHTML = `
            <div class="mt-2">
              <p class="text-sm text-gray-600 mb-2">Selected Photo:</p>
              <img src="${e.target.result}" alt="Preview" class="w-20 h-20 object-cover rounded-lg border" />
            </div>
          `;
        };
        reader.readAsDataURL(file);
      } else {
        preview.innerHTML = '';
      }
    });
  </script>
</body>
</html>