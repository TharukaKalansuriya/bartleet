<?php
session_start();

// Define allowed roles
$allowed_roles = ['admin', 'manager'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    
    // Redirect to the login page if not authorized
    header("Location: index.php");
    exit();
}
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Annual Maintenance Contracts</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');">
<?php include "navbar.php" ?>
 <!-- Header with Logo and Title in a Blurred Background -->
 <section class="flex items-center justify-center pt-10 px-4">
    <div class="backdrop-blur-md bg-white/20 rounded-2xl shadow-xl p-6 flex items-center gap-6 mb-6">
      <img src="img/logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-red-700 ">BC–Agro Tronics</h1>
        <p class="text-xl text-red-900">Annual Maintainance Records</p>
      </div>
    </div>
  </section>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

    <!-- Factories List -->
    <div class="col-span-1 bg-white rounded-xl shadow-md p-4 overflow-y-auto max-h-[500px]">
      <h2 class="text-lg font-semibold mb-2 text-red-500">Factories</h2>
      <ul id="factoriesList" class="space-y-2">
        <!-- Will be populated dynamically -->
        <li class="p-2 bg-gray-100 rounded cursor-pointer hover:bg-red-200 text-center">Loading...</li>
      </ul>
    </div>

    <!-- Serial Numbers List -->
    <div class="col-span-1 bg-white rounded-xl shadow-md p-4 overflow-y-auto max-h-[500px]">
      <h2 class="text-lg font-semibold mb-2 text-red-500">Serial Numbers</h2>
      <ul id="serialList" class="space-y-2">
        <!-- Will be populated dynamically -->
        <li class="p-2 bg-gray-100 rounded text-center">Select a factory or team</li>
      </ul>
    </div>

    <!-- Service Round Info -->
    <div class="col-span-1 bg-white rounded-xl shadow-md p-4 max-h-[500px] overflow-y-auto">
      <h2 class="text-lg font-semibold mb-2 text-red-500">Service Rounds</h2>
      <div id="serviceInfo" class="space-y-4">
        <!-- Will be populated dynamically -->
        <div class="bg-gray-50 p-3 rounded shadow text-center">
          <p>Select a serial number to view details</p>
        </div>
      </div>
    </div>

    <!-- Teams List -->
    <div class="col-span-1 bg-white rounded-xl shadow-md p-4 overflow-y-auto max-h-[500px]">
      <h2 class="text-lg font-semibold mb-2 text-red-500">Teams</h2>
      <ul id="teamsList" class="space-y-2">
        <!-- Will be populated dynamically -->
        <li class="p-2 bg-gray-100 rounded text-center">Loading...</li>
      </ul>
    </div>
  </div>

  <!-- FSR Detail Modal -->
  <div id="fsrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl max-h-[80vh] overflow-y-auto">
      <div class="flex justify-between mb-4">
        <h2 class="text-2xl font-bold text-red-600">FSR Details</h2>
        <button id="closeModal" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div id="fsrDetails" class="space-y-4">
        <!-- FSR details will go here -->
      </div>
    </div>
  </div>

<script>
  $(document).ready(function() {
    // Active selections
    let activeFactory = null;
    let activeTeam = null;
    let activeSerial = null;
    
    // Load factories on page load
    loadFactories();
    loadTeams();
    
    // Event delegation for dynamic elements
    $(document).on('click', '#factoriesList li', function() {
      const facId = $(this).data('facid');
      selectFactory(facId, $(this).text());
      
      // Update UI to show this factory is selected
      $('#factoriesList li').removeClass('bg-red-400').addClass('bg-red-100');
      $(this).removeClass('bg-red-200').addClass('bg-red-400');
    });
    
    $(document).on('click', '#teamsList li', function() {
      const teamId = $(this).data('teamid');
      selectTeam(teamId, $(this).text());
      
      // Update UI to show this team is selected
      $('#teamsList li').removeClass('bg-blue-300').addClass('bg-blue-100');
      $(this).removeClass('bg-blue-100').addClass('bg-blue-300');
    });
    
    $(document).on('click', '#serialList li', function() {
      const serialNo = $(this).data('serialno');
      selectSerial(serialNo);
      
      // Update UI to show this serial is selected
      $('#serialList li').removeClass('bg-gray-300').addClass('bg-gray-100');
      $(this).removeClass('bg-gray-100').addClass('bg-gray-300');
    });
    
    // Close modal
    $('#closeModal').click(function() {
      $('#fsrModal').hide();
    });
    
    // Click outside modal to close
    $(window).click(function(event) {
      if ($(event.target).is('#fsrModal')) {
        $('#fsrModal').hide();
      }
    });
    
    // Functions to load data
    function loadFactories() {
      $.ajax({
        url: 'get_factories.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
          const factoriesList = $('#factoriesList');
          factoriesList.empty();
          
          if (data.length === 0) {
            factoriesList.append('<li class="p-2 bg-gray-100 rounded text-center">No factories found</li>');
            return;
          }
          
          data.forEach(factory => {
            factoriesList.append(`
              <li class="p-2 bg-red-100 rounded cursor-pointer hover:bg-red-200" 
                  data-facid="${factory.FacId}">
                ${factory.FacName}
              </li>
            `);
          });
        },
        error: function() {
          $('#factoriesList').html('<li class="p-2 bg-red-100 rounded text-center">Error loading factories</li>');
        }
      });
    }
    
    function loadTeams() {
      $.ajax({
        url: 'get_teams.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
          const teamsList = $('#teamsList');
          teamsList.empty();
          
          if (data.length === 0) {
            teamsList.append('<li class="p-2 bg-gray-100 rounded text-center">No teams found</li>');
            return;
          }
          
          data.forEach(team => {
            teamsList.append(`
              <li class="p-2 bg-blue-100 rounded cursor-pointer hover:bg-blue-200" 
                  data-teamid="${team.teamId}">
                ${team.teamName}
              </li>
            `);
          });
        },
        error: function() {
          $('#teamsList').html('<li class="p-2 bg-blue-100 rounded text-center">Error loading teams</li>');
        }
      });
    }
    
   // Modified selectFactory function with better error handling
function selectFactory(facId, facName) {
  activeFactory = facId;
  activeTeam = null;
  
  // Reset team selection visual
  $('#teamsList li').removeClass('bg-blue-300').addClass('bg-blue-100');
  
  // Show loading indicator
  $('#serialList').html('<li class="p-2 bg-gray-100 rounded text-center">Loading serial numbers...</li>');
  
  // Load serial numbers for this factory
  $.ajax({
    url: 'get_serials.php',
    method: 'GET',
    data: { facId: facId },
    dataType: 'json',
    success: function(data) {
      console.log("Serial numbers response:", data); // Debug log
      
      const serialList = $('#serialList');
      serialList.empty();
      
      if (!data || data.error) {
        serialList.append(`<li class="p-2 bg-red-100 rounded text-center">Error: ${data.error || 'Unknown error'}</li>`);
        return;
      }
      
      if (data.length === 0) {
        serialList.append('<li class="p-2 bg-gray-100 rounded text-center">No serial numbers for this factory</li>');
        return;
      }
      
      data.forEach(serial => {
        // Make sure we handle potential differences in property casing
        const serialNo = serial.SerialNo || serial.serialNo || serial.serialno || '';
        
        if (!serialNo) {
          console.warn("Found a serial record without SerialNo property:", serial);
          return; // Skip this record
        }
        
        serialList.append(`
          <li class="p-2 bg-gray-100 rounded cursor-pointer hover:bg-gray-200" 
              data-serialno="${serialNo}">
            ${serialNo}
          </li>
        `);
      });
    },
    error: function(xhr, status, error) {
      console.error("AJAX error:", status, error);
      console.log("Response:", xhr.responseText);
      $('#serialList').html(`<li class="p-2 bg-red-100 rounded text-center">Error: ${error || 'Could not load serial numbers'}</li>`);
    }
  });
  
  // Clear service info when selecting a new factory
  $('#serviceInfo').html('<div class="bg-gray-50 p-3 rounded shadow text-center"><p>Select a serial number to view details</p></div>');
}
    
    function selectTeam(teamId, teamName) {
      activeTeam = teamId;
      activeFactory = null;
      
      // Reset factory selection visual
      $('#factoriesList li').removeClass('bg-red-300').addClass('bg-red-100');
      
      // Load factories for this team
      $.ajax({
        url: 'get_team_factories.php',
        method: 'GET',
        data: { teamId: teamId },
        dataType: 'json',
        success: function(data) {
          // Highlight the factories this team works on
          $('#factoriesList li').removeClass('bg-red-300').addClass('bg-red-100');
          
          data.forEach(factory => {
            $(`#factoriesList li[data-facid="${factory.FacId}"]`).removeClass('bg-red-100').addClass('bg-red-200');
          });
        }
      });
      
      // Load serial numbers for this team
      $.ajax({
        url: 'get_team_serials.php', 
        method: 'GET',
        data: { teamId: teamId },
        dataType: 'json',
        success: function(data) {
          const serialList = $('#serialList');
          serialList.empty();
          
          if (data.length === 0) {
            serialList.append('<li class="p-2 bg-gray-100 rounded text-center">No serial numbers for this team</li>');
            return;
          }
          
          data.forEach(serial => {
            serialList.append(`
              <li class="p-2 bg-gray-100 rounded cursor-pointer hover:bg-gray-200" 
                  data-serialno="${serial.SerialNo}">
                ${serial.SerialNo}
              </li>
            `);
          });
        },
        error: function() {
          $('#serialList').html('<li class="p-2 bg-gray-100 rounded text-center">Error loading serial numbers</li>');
        }
      });
      
      // Clear service info when selecting a new team
      $('#serviceInfo').html('<div class="bg-gray-50 p-3 rounded shadow text-center"><p>Select a serial number to view details</p></div>');
    }
    
    function selectSerial(serialNo) {
  activeSerial = serialNo;
  
  // Show loading indicator
  $('#serviceInfo').html('<div class="bg-gray-50 p-3 rounded shadow text-center"><p>Loading service records...</p></div>');
  
  // Load service rounds for this serial number
  $.ajax({
    url: 'get_service_rounds.php',
    method: 'GET',
    data: { 
      serialNo: serialNo,
      facId: activeFactory,
      teamId: activeTeam
    },
    dataType: 'json',
    success: function(data) {
      console.log("Response data:", data);  // Debug info
      
      const serviceInfo = $('#serviceInfo');
      serviceInfo.empty();
      
      // Check for explicit error message
      if (data.error) {
        serviceInfo.html(`<div class="bg-yellow-50 p-3 rounded shadow text-center">
          <p>Error: ${data.error}</p>
        </div>`);
        return;
      }
      
      // Check if we have valid data
      if (!data.summary || !data.fsrs || data.fsrs.length === 0) {
        serviceInfo.html(`<div class="bg-gray-50 p-3 rounded shadow text-center">
          <p>No service records found for serial ${serialNo}</p>
          <p class="text-sm text-gray-500 mt-1">Please verify the serial number and filter settings</p>
        </div>`);
        return;
      }
      
      // Display summary information
      const summary = data.summary;
      serviceInfo.append(`
        <div class="bg-gray-50 p-3 rounded shadow">
          <p class="text-lg font-bold">${summary.SerialNo}</p>
          <p><strong>Factory:</strong> ${summary.FacName}</p>
          <p><strong>Service Rounds:</strong> ${summary.RoundCount}</p>
          <p><strong>Latest Service:</strong> ${summary.LastServiceDate || 'N/A'}</p>
          <p><strong>Status:</strong> <span class="px-2 py-1 rounded ${summary.Status === 'Active' ? 'bg-green-200' : 'bg-yellow-200'}">${summary.Status || 'Pending'}</span></p>
        </div>
      `);
      
      // Show FSR records
      serviceInfo.append('<h3 class="text-md font-semibold mt-4 mb-2">Service Records:</h3>');
      
      data.fsrs.forEach(fsr => {
        serviceInfo.append(`
          <div class="bg-white border border-gray-200 p-3 rounded shadow-sm hover:bg-gray-50 cursor-pointer fsr-record mb-2" data-fsrid="${fsr.FSRId}">
            <div class="flex justify-between">
              <span class="font-medium">FSR #${fsr.FSRNo}</span>
              <span class="text-sm text-gray-500">${fsr.ServiceDate || 'N/A'}</span>
            </div>
            <p class="text-sm truncate">${fsr.WorkDescription?.substring(0, 60) || 'No description'}${fsr.WorkDescription?.length > 60 ? '...' : ''}</p>
          </div>
        `);
      });
      
      // Event for FSR detail view
      $('.fsr-record').click(function() {
        const fsrId = $(this).data('fsrid');
        showFSRDetails(fsrId);
      });
    },
    error: function(xhr, status, error) {
      console.error("AJAX error:", status, error);
      $('#serviceInfo').html(`<div class="bg-red-50 p-3 rounded shadow text-center">
        <p>Error loading service information</p>
        <p class="text-sm text-gray-500 mt-1">${error}</p>
      </div>`);
    }
  });
}
    
    function showFSRDetails(fsrId) {
      $.ajax({
        url: 'get_fsr_details2.php',
        method: 'GET',
        data: { fsrId: fsrId },
        dataType: 'json',
        success: function(data) {
          if (!data) {
            $('#fsrDetails').html('<p class="text-center">No details available</p>');
            $('#fsrModal').show();
            return;
          }
          
          let servicePersons = '';
          if (data.servicePersons && data.servicePersons.length > 0) {
            servicePersons = '<div class="mt-4"><h3 class="font-semibold">Service Personnel:</h3><ul class="list-disc pl-5">';
            data.servicePersons.forEach(person => {
              servicePersons += `<li>${person.Name}</li>`;
            });
            servicePersons += '</ul></div>';
          }
          
          $('#fsrDetails').html(`
            <div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p><strong>FSR Number:</strong> ${data.FSRNo}</p>
                  <p><strong>Serial Number:</strong> ${data.SerialNo}</p>
                  <p><strong>Machine:</strong> ${data.Machine || 'N/A'}</p>
                  <p><strong>Make/Model:</strong> ${data.MakeModel || 'N/A'}</p>
                </div>
                <div>
                  <p><strong>Factory:</strong> ${data.FacName}</p>
                  <p><strong>Inspection Type:</strong> ${data.InspectionType || 'Standard'}</p>
                  <p><strong>Departure from Colombo:</strong> ${data.DepartureFromColombo || 'N/A'}</p>
                  <p><strong>Arrival at Factory:</strong> ${data.ArrivalAtFactory || 'N/A'}</p>
                  <p><strong>Departure from Factory:</strong> ${data.DepartureFromFactory || 'N/A'}</p>
                </div>
              </div>
              
              <div class="mt-4">
                <h3 class="font-semibold">Work Description:</h3>
                <div class="p-3 bg-gray-50 rounded mt-2">
                  ${data.WorkDescription || 'No description available'}
                </div>
              </div>
              
              ${servicePersons}
            </div>
          `);
          
          $('#fsrModal').show();
        },
        error: function() {
          $('#fsrDetails').html('<p class="text-center text-red-500">Error loading FSR details</p>');
          $('#fsrModal').show();
        }
      });
    }
  });
</script>
<?php include "footer.php" ?>
</body>
</html>