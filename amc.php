<?php
session_start();

// Define allowed roles
$allowed_roles = ['admin', 'manager'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
            /* Add a subtle overlay to ensure text readability */
            position: relative;
        }
        
        /* Add a subtle dark overlay to the background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            z-index: -1;
        }
        
        /* Enhanced glass effect for cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        /* Header glass effect */
        .glass-header {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Modal glass effect */
        .glass-modal {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* Scrollbar styling for glass cards */
        .glass-card::-webkit-scrollbar {
            width: 6px;
        }
        
        .glass-card::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        
        .glass-card::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }
        
        .glass-card::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="min-h-screen">
    
    <?php include "navbar.php" ?>
    <!-- Back Button -->
<div class="absolute top-10 right-10 z-50">
    <img 
        src="img/back.png" 
        onclick="history.back()" 
        alt="Back" 
        class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
    />
</div>
    <!-- Header Section -->
    <section class="py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="glass-header rounded-2xl shadow-xl p-6">
                <div class="flex items-center gap-6">
                    <img src="img/logo.png" alt="Logo" class="w-20 h-16 object-contain" />
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-white drop-shadow-lg">BC–Agro Tronics</h1>
                        <p class="text-lg text-white/90 mt-1 drop-shadow">Annual Maintenance Records</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                <!-- Locations Panel -->
                <div class="glass-card rounded-xl shadow-lg">
                    <div class="p-4 border-b border-white/20">
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2 drop-shadow">
                            <i class="fas fa-map-marker-alt text-blue-300"></i>
                            Locations
                        </h2>
                    </div>
                    <div class="p-4">
                        <div id="locationsList" class="space-y-2 max-h-96 overflow-y-auto">
                            <div class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-300"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Factories Panel -->
                <div class="glass-card rounded-xl shadow-lg">
                    <div class="p-4 border-b border-white/20">
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2 drop-shadow">
                            <i class="fas fa-industry text-green-300"></i>
                            Factories
                        </h2>
                        <!-- Search Box -->
                        <div class="mt-3 relative">
                            <input 
                                type="text" 
                                id="factorySearch" 
                                placeholder="Search factories..." 
                                class="w-full pl-9 pr-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 
                                       rounded-lg focus:ring-2 focus:ring-blue-300 focus:border-transparent 
                                       text-sm text-white placeholder-white/70"
                            >
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/70"></i>
                        </div>
                    </div>
                    <div class="p-4">
                        <div id="factoriesList" class="space-y-2 max-h-96 overflow-y-auto">
                            <div class="text-center py-4 text-white/70">
                                Select a location to view factories
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Serial Numbers Panel -->
                <div class="glass-card rounded-xl shadow-lg">
                    <div class="p-4 border-b border-white/20">
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2 drop-shadow">
                            <i class="fas fa-barcode text-purple-300"></i>
                            Serial Numbers
                        </h2>
                    </div>
                    <div class="p-4">
                        <div id="serialList" class="space-y-2 max-h-96 overflow-y-auto">
                            <div class="text-center py-4 text-white/70">
                                Select a factory to view serial numbers
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Records Panel -->
                <div class="glass-card rounded-xl shadow-lg">
                    <div class="p-4 border-b border-white/20">
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2 drop-shadow">
                            <i class="fas fa-tools text-orange-300"></i>
                            Service Records
                        </h2>
                    </div>
                    <div class="p-4">
                        <div id="serviceInfo" class="space-y-4 max-h-96 overflow-y-auto">
                            <div class="text-center py-4 text-white/70">
                                Select a serial number to view service records
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FSR Detail Modal -->
    <div id="fsrModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="glass-modal rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">FSR Details</h2>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="fsrDetails" class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                <!-- FSR details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // State management
        const state = {
            activeLocation: null,
            activeFactory: null,
            activeSerial: null,
            allFactories: []
        };

        // Initialize
        loadLocations();

        // Event handlers
        $('#factorySearch').on('input', debounce(handleFactorySearch, 300));
        $('#closeModal').click(() => $('#fsrModal').hide());
        $(window).click(event => {
            if ($(event.target).is('#fsrModal')) {
                $('#fsrModal').hide();
            }
        });

        // Event delegation for dynamic elements
        $(document).on('click', '.location-item', handleLocationClick);
        $(document).on('click', '.factory-item', handleFactoryClick);
        $(document).on('click', '.serial-item', handleSerialClick);
        $(document).on('click', '.fsr-record', handleFSRClick);

        // Utility functions
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function showLoading(containerId) {
            $(`#${containerId}`).html(`
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-300"></div>
                </div>
            `);
        }

        function showError(containerId, message) {
            $(`#${containerId}`).html(`
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-red-400 mb-2"></i>
                    <p class="text-red-300">${message}</p>
                </div>
            `);
        }

        // Load functions
        function loadLocations() {
            showLoading('locationsList');
            
            $.ajax({
                url: 'get_locations.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    const locationsList = $('#locationsList');
                    locationsList.empty();
                    
                    if (!data || data.length === 0) {
                        locationsList.html('<div class="text-center py-4 text-white/70">No locations found</div>');
                        return;
                    }
                    
                    data.forEach(location => {
                        locationsList.append(`
                            <div class="location-item p-3 rounded-lg cursor-pointer transition-all duration-200 
                                       bg-white/10 hover:bg-blue-500/30 border border-transparent hover:border-blue-300/50 backdrop-blur-sm" 
                                 data-location="${location.Location}">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-blue-300"></i>
                                    <span class="font-medium text-white">${location.Location}</span>
                                </div>
                                <div class="text-sm text-white/70 mt-1">
                                    ${location.FactoryCount} factories
                                </div>
                            </div>
                        `);
                    });
                },
                error: () => showError('locationsList', 'Error loading locations')
            });
        }

        function loadFactoriesByLocation(location) {
            showLoading('factoriesList');
            
            $.ajax({
                url: 'get_factories_by_location.php',
                method: 'GET',
                data: { location: location },
                dataType: 'json',
                success: function(data) {
                    state.allFactories = data || [];
                    displayFactories(state.allFactories);
                },
                error: () => showError('factoriesList', 'Error loading factories')
            });
        }

        function displayFactories(factories) {
            const factoriesList = $('#factoriesList');
            factoriesList.empty();
            
            if (!factories || factories.length === 0) {
                factoriesList.html('<div class="text-center py-4 text-white/70">No factories found</div>');
                return;
            }
            
            factories.forEach(factory => {
                factoriesList.append(`
                    <div class="factory-item p-3 rounded-lg cursor-pointer transition-all duration-200 
                               bg-white/10 hover:bg-green-500/30 border border-transparent hover:border-green-300/50 backdrop-blur-sm" 
                         data-facid="${factory.FacId}">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-industry text-green-300"></i>
                            <span class="font-medium text-white">${factory.FacName}</span>
                        </div>
                    </div>
                `);
            });
        }

        function loadSerialNumbers(facId) {
            showLoading('serialList');
            
            $.ajax({
                url: 'get_serials.php',
                method: 'GET',
                data: { facId: facId },
                dataType: 'json',
                success: function(data) {
                    const serialList = $('#serialList');
                    serialList.empty();
                    
                    if (!data || data.length === 0) {
                        serialList.html('<div class="text-center py-4 text-white/70">No serial numbers found</div>');
                        return;
                    }
                    
                    data.forEach(serial => {
                        const serialNo = serial.SerialNo || serial.serialNo || '';
                        if (serialNo) {
                            serialList.append(`
                                <div class="serial-item p-3 rounded-lg cursor-pointer transition-all duration-200 
                                           bg-white/10 hover:bg-purple-500/30 border border-transparent hover:border-purple-300/50 backdrop-blur-sm" 
                                     data-serialno="${serialNo}">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-barcode text-purple-300"></i>
                                        <span class="font-medium text-white">${serialNo}</span>
                                    </div>
                                </div>
                            `);
                        }
                    });
                },
                error: () => showError('serialList', 'Error loading serial numbers')
            });
        }

        function loadServiceRecords(serialNo) {
            showLoading('serviceInfo');
            
            $.ajax({
                url: 'get_service_rounds.php',
                method: 'GET',
                data: { 
                    serialNo: serialNo,
                    facId: state.activeFactory
                },
                dataType: 'json',
                success: function(data) {
                    const serviceInfo = $('#serviceInfo');
                    serviceInfo.empty();
                    
                    if (data.error) {
                        showError('serviceInfo', data.error);
                        return;
                    }
                    
                    if (!data.summary || !data.fsrs || data.fsrs.length === 0) {
                        serviceInfo.html('<div class="text-center py-4 text-white/70">No service records found</div>');
                        return;
                    }
                    
                    // Display summary
                    const summary = data.summary;
                    serviceInfo.append(`
                        <div class="bg-gradient-to-r from-blue-500/20 to-purple-500/20 backdrop-blur-sm p-4 rounded-lg border border-blue-300/30">
                            <h3 class="text-lg font-bold text-white mb-3">${summary.SerialNo}</h3>
                            <div class="grid grid-cols-1 gap-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-white/70">Factory:</span>
                                    <span class="font-medium text-white">${summary.FacName}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-white/70">Service Rounds:</span>
                                    <span class="font-medium text-white">${summary.RoundCount}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-white/70">Latest Service:</span>
                                    <span class="font-medium text-white">${summary.LastServiceDate || 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-white/70">Status:</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium ${
                                        summary.Status === 'Active' ? 'bg-green-500/30 text-green-200 border border-green-300/50' : 'bg-yellow-500/30 text-yellow-200 border border-yellow-300/50'
                                    }">${summary.Status || 'Completed'}</span>
                                </div>
                            </div>
                        </div>
                    `);
                    
                    // Display FSR records
                    serviceInfo.append('<h4 class="text-md font-semibold mt-4 mb-3 text-white">Service Records:</h4>');
                    
                    data.fsrs.forEach(fsr => {
                        serviceInfo.append(`
                            <div class="fsr-record bg-white/10 backdrop-blur-sm border border-white/20 p-3 rounded-lg shadow-sm 
                                       hover:shadow-md cursor-pointer transition-all duration-200 hover:border-orange-300/50 hover:bg-orange-500/20 mb-2" 
                                 data-fsrid="${fsr.FSRId}">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-medium text-white">FSR #${fsr.FSRNo}</span>
                                    <span class="text-sm text-white/70">${fsr.ServiceDate || 'N/A'}</span>
                                </div>
                                <p class="text-sm text-white/80 truncate">
                                    ${fsr.WorkDescription?.substring(0, 60) || 'No description'}${fsr.WorkDescription?.length > 60 ? '...' : ''}
                                </p>
                            </div>
                        `);
                    });
                },
                error: () => showError('serviceInfo', 'Error loading service records')
            });
        }

        // Event handlers
        function handleLocationClick() {
            const location = $(this).data('location');
            
            // Update UI
            $('.location-item').removeClass('bg-blue-500/30 border-blue-300/50').addClass('bg-white/10');
            $(this).removeClass('bg-white/10').addClass('bg-blue-500/30 border-blue-300/50');
            
            // Update state
            state.activeLocation = location;
            state.activeFactory = null;
            state.activeSerial = null;
            
            // Reset other panels
            $('#serialList').html('<div class="text-center py-4 text-white/70">Select a factory to view serial numbers</div>');
            $('#serviceInfo').html('<div class="text-center py-4 text-white/70">Select a serial number to view service records</div>');
            
            // Load factories for this location
            loadFactoriesByLocation(location);
        }

        function handleFactoryClick() {
            const facId = $(this).data('facid');
            
            // Update UI
            $('.factory-item').removeClass('bg-green-500/30 border-green-300/50').addClass('bg-white/10');
            $(this).removeClass('bg-white/10').addClass('bg-green-500/30 border-green-300/50');
            
            // Update state
            state.activeFactory = facId;
            state.activeSerial = null;
            
            // Reset service panel
            $('#serviceInfo').html('<div class="text-center py-4 text-white/70">Select a serial number to view service records</div>');
            
            // Load serial numbers
            loadSerialNumbers(facId);
        }

        function handleSerialClick() {
            const serialNo = $(this).data('serialno');
            
            // Update UI
            $('.serial-item').removeClass('bg-purple-500/30 border-purple-300/50').addClass('bg-white/10');
            $(this).removeClass('bg-white/10').addClass('bg-purple-500/30 border-purple-300/50');
            
            // Update state
            state.activeSerial = serialNo;
            
            // Load service records
            loadServiceRecords(serialNo);
        }

        function handleFSRClick() {
            const fsrId = $(this).data('fsrid');
            showFSRDetails(fsrId);
        }

        function handleFactorySearch() {
            const searchTerm = $('#factorySearch').val().toLowerCase();
            
            if (!searchTerm) {
                displayFactories(state.allFactories);
                return;
            }
            
            const filteredFactories = state.allFactories.filter(factory => 
                factory.FacName.toLowerCase().includes(searchTerm)
            );
            
            displayFactories(filteredFactories);
        }

        function showFSRDetails(fsrId) {
            $.ajax({
                url: 'get_fsr_details2.php',
                method: 'GET',
                data: { fsrId: fsrId },
                dataType: 'json',
                success: function(data) {
                    if (!data) {
                        $('#fsrDetails').html('<p class="text-center text-slate-500">No details available</p>');
                        $('#fsrModal').show();
                        return;
                    }
                    
                    let servicePersons = '';
                    if (data.servicePersons && data.servicePersons.length > 0) {
                        servicePersons = `
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-slate-800 mb-3">Service Personnel:</h3>
                                <div class="bg-slate-50 rounded-lg p-4">
                                    <ul class="space-y-2">
                                        ${data.servicePersons.map(person => `
                                            <li class="flex items-center gap-2">
                                                <i class="fas fa-user text-blue-600"></i>
                                                <span>${person.Name}</span>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        `;
                    }
                    
                    $('#fsrDetails').html(`
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="bg-slate-50 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-slate-800 mb-3">Basic Information</h3>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">FSR Number:</span>
                                                <span class="font-medium">${data.FSRNo}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Serial Number:</span>
                                                <span class="font-medium">${data.SerialNo}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Machine:</span>
                                                <span class="font-medium">${data.Machine || 'N/A'}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Make/Model:</span>
                                                <span class="font-medium">${data.MakeModel || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="bg-slate-50 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-slate-800 mb-3">Service Details</h3>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Factory:</span>
                                                <span class="font-medium">${data.FacName}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Inspection Type:</span>
                                                <span class="font-medium">${data.InspectionType || 'Standard'}</span>
                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Departure:</span>
                                                <span class="font-medium">${data.DepartureFromColombo || 'N/A'}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Arrival:</span>
                                                <span class="font-medium">${data.ArrivalAtFactory || 'N/A'}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Departure:</span>
                                                <span class="font-medium">${data.DepartureFromFactory || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-slate-800 mb-3">Work Description</h3>
                                <div class="bg-white rounded p-4 border border-slate-200">
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