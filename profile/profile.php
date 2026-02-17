<?php

require_once __DIR__ . '/../config/bootstrap.php';
$isAdmin = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client List</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    

    <?php include __DIR__ . '/../navigation/navigation.php'; ?>


     

    <div id="menue_admin" <?php echo $isAdmin ? '' : 'style="display: none;"'; ?>>
        <h1 class="admin-title">Menu</h1>
        <div class="button-container">
            <button onclick="get_list()">User Management</button>
            <button onclick="get_car_list()">Vehicle Management</button>
            <button onclick="get_motorcycle_list()">Motorcycle Management</button>
        </div>
    </div>


    <div id="profile_list">
        <h1 class="profile-title">Customer List</h1>
        

        <table class="client-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody id="body-profile-list">


        </tbody>
        </table>
    </div>

    

        <div id="profile_admin">
            <h1 style="text-align: center;">Customer Details</h1>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody id="body-profile-details">

            </tbody>
            </table>
        </div>



        <div id="profile_user" <?php echo $isAdmin ? 'style="display: none;"' : ''; ?>>
            <h1 style="text-align: center;">Profile Details</h1>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody id="body-profile-details-user">

            </tbody>
            </table>
        </div>


        <div id="car_list_form">
            <h1 class="profile-title">Vehicle List</h1>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price per day</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody id="body-profile-details-cars-list">
                    
                </tbody>
            </table>
        </div>
        
        <div id="motorcycle_list_form">
            <h1 class="profile-title">Motorcycle List</h1>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price per day</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody id="body-profile-details-motorcycle-list">
                </tbody>
            </table>
        </div>

        



        <div id="car_details">
            <h1 style="text-align: center;">Add Vehicle</h1>
            <table>
                <tbody>
                    <tr>
                        <td><label for="name_new">Name</label></td>
                        <td><input type="text" id="name_new" placeholder="Name"></td>
                    </tr>
                    <tr>
                        <td><label for="price_per_day_new">Price per day</label></td>
                        <td><input type="number" id="price_per_day_new" placeholder="Price per day"></td>
                    </tr>
                    <tr>
                        <td><label for="fuel_new">Fuel</label></td>
                        <td><input type="text" id="fuel_new" placeholder="Fuel"></td>
                    </tr>
                    <tr>
                        <td><label for="seating_capacity_new">Seating capacity</label></td>
                        <td><input type="number" id="seating_capacity_new" placeholder="Seating capacity"></td>
                    </tr>
                    <tr>
                        <td><label for="engine_new">Engine size</label></td>
                        <td><input type="text" id="engine_new" placeholder="Engine size"></td>
                    </tr>
                    <tr>
                        <td><label for="transmission_new">Transmission</label></td>
                        <td><input type="text" id="transmission_new" placeholder="Transmission"></td>
                    </tr>
                    <tr>
                        <td><label for="year_new">Year of manufacture</label></td>
                        <td><input type="number" id="year_new" placeholder="Year of manufacture"></td>
                    </tr>
                    <tr>
                        <td><label for="bluetooth_new">Bluetooth</label></td>
                        <td><input type="checkbox" id="bluetooth_new"></td>
                    </tr>
                    <tr>
                        <td><label for="gps_new">GPS</label></td>
                        <td><input type="checkbox" id="gps_new"></td>
                    </tr>
                    <tr>
                        <td><label for="color_new">Color</label></td>
                        <td><input type="text" id="color_new" placeholder="Color"></td>
                    </tr>
                    <tr>
                        <td><label for="type_new">Type</label></td>
                        <td><input type="text" id="type_new" placeholder="Type"></td>
                    </tr>
                    <tr>
                        <td><label for="profile_pictures_new">Vehicle photos</label></td>
                        <td>
                            <label for="profile_pictures_new" class="custom-file-upload">Add profile photo</label>
                            <input type="file" id="profile_pictures_new" name="profile_pictures[]" accept="image/*" multiple style="display: none;">
                        </td>                    </tr>
           
                        <td colspan="2" style="text-align: center;">
                            <button onclick="submitNewCar()">Add Vehicle</button>
                            <button onclick="cancelAddCar()">Cancel</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>




        <div id="motorcycle_details">
            <h1 style="text-align: center;">Add Motorcycle</h1>
            <table>
                <tbody>
                    <tr>
                        <td><label for="moto_name_new">Name</label></td>
                        <td><input type="text" id="moto_name_new" placeholder="Name"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_price_per_day_new">Price per day</label></td>
                        <td><input type="number" id="moto_price_per_day_new" placeholder="Price per day"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_fuel_new">Fuel</label></td>
                        <td><input type="text" id="moto_fuel_new" placeholder="Fuel"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_engine_cc_new">Engine (cc)</label></td>
                        <td><input type="number" id="moto_engine_cc_new" placeholder="Engine (cc)"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_transmission_new">Transmission</label></td>
                        <td><input type="text" id="moto_transmission_new" placeholder="Transmission"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_year_new">Year of manufacture</label></td>
                        <td><input type="number" id="moto_year_new" placeholder="Year of manufacture"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_abs_new">ABS</label></td>
                        <td><input type="checkbox" id="moto_abs_new"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_color_new">Color</label></td>
                        <td><input type="text" id="moto_color_new" placeholder="Color"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_type_new">Type</label></td>
                        <td><input type="text" id="moto_type_new" placeholder="Type"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_weight_kg_new">Weight (kg)</label></td>
                        <td><input type="number" id="moto_weight_kg_new" placeholder="Weight (kg)"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_seat_height_mm_new">Seat height (mm)</label></td>
                        <td><input type="number" id="moto_seat_height_mm_new" placeholder="Seat height (mm)"></td>
                    </tr>
                    <tr>
                        <td><label for="moto_pictures_new">Motorcycle photos</label></td>
                        <td>
                            <label for="moto_pictures_new" class="custom-file-upload">Add profile photo</label>
                            <input type="file" id="moto_pictures_new" name="moto_pictures[]" accept="image/*" multiple style="display: none;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button onclick="submitNewMotorcycle()">Add Motorcycle</button>
                            <button onclick="cancelAddMotorcycle()">Cancel</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        
        
        <div id="carDetailsModal" class="modal">
            <div class="carDetails">
              <div id="imageContainer">
      
                <button id="leftArrow"><</button>
                <img id="carImage" alt="car" >
                <button id="rightArrow">></button>
      
              </div>
              <div id="detailsContainer">
                <h2 id="carName"></h2>
                <div>
                    <label class="staticLabel">Year: </label>
                    <label id="carYear" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Transmission: </label>
                    <label id="carTransmission" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Engine: </label>
                    <label id="carEngine" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Seating capacity i ndenjesve: </label>
                    <label id="carSeatingCapacity" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Color: </label>
                    <label id="carColor" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Bluetooth: </label>
                    <label id="carBluetooth" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">GPS: </label>
                    <label id="carGPS" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Type: </label>
                    <label id="carType" class="detailLabel"></label>
                </div>
      
                <div>
                    <label class="staticLabel">Daily price: </label>
                    <label id="carPrice" class="detailLabel"></label>
                    <label class="detailLabel"> EUR</label>
                    
                </div>
      
                <div>
                    <label class="staticLabel">Pickup date: </label>
                    <input type="date" id="startDate" required>
                </div>
                <div>
                    <label class="staticLabel">Return date:</label>
                    <input type="date" id="endDate" required>
                </div>
                <div>
                    <label class="staticLabel">Total rental days: </label>
                    <span id="diteTotale"></span>
      
                </div>
                <span id="diteTotale"></span>
                
                
                
                <button id="fshiButton">
                    FSHI
                </button>
                <button id="dilButton">
                    DIL
                </button>
              </div>
      
          </div>
        </div>
        
        <div id="motorcycleDetailsModal" class="modal">
            <div class="carDetails">
              <div id="motorcycleImageContainer">
                <button id="motoLeftArrow"><</button>
                <img id="motorcycleImage" alt="motorcycle" >
                <button id="motoRightArrow">></button>
              </div>
              <div id="motorcycleDetailsContainer">
                <h2 id="motorcycleName"></h2>
                <div>
                    <label class="staticLabel">Year: </label>
                    <label id="motorcycleYear" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Transmission: </label>
                    <label id="motorcycleTransmission" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Engine (cc): </label>
                    <label id="motorcycleEngineCc" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Seat height (mm): </label>
                    <label id="motorcycleSeatHeight" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Weight (kg): </label>
                    <label id="motorcycleWeight" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">ABS: </label>
                    <label id="motorcycleAbs" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Fuel: </label>
                    <label id="motorcycleFuel" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Color: </label>
                    <label id="motorcycleColor" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Type: </label>
                    <label id="motorcycleType" class="detailLabel"></label>
                </div>
                <div>
                    <label class="staticLabel">Daily price: </label>
                    <label id="motorcyclePrice" class="detailLabel"></label>
                    <label class="detailLabel"> EUR</label>
                </div>
                <button id="motorcycleDeleteButton">
                    FSHI
                </button>
                <button id="motorcycleCloseButton">
                    DIL
                </button>
              </div>
      
          </div>
        </div>
        
        <div id="bookingHistoryModal" class="modal">
            <div id="bokingHistory" class="bokingHistory">
                <h2>Booking history</h2>
                <div id="bokingHistoryContainer"></div>
                <button id="exitButton">Exit</button>
            </div>
        </div>
        

</body>

<script src="../makinat/carDetails.js"></script>
<script src="./motorcycleDetails.js"></script>


<script src="profile.js"></script>

<script src="../registerLogin/sessionTimeout.js"></script>




<script>
    
</script>


</html>
