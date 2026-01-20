<?php
// Profile page: shows admin tools or user profile based on role.
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


     <!-- here we will  display the admin menu only if we have an admin session 
      otherwise we will hide it using css -->
    <div id="menue_admin" <?php echo $isAdmin ? '' : 'style="display: none;"'; ?>>
        <h1 class="admin-title">Menu</h1>
        <div class="button-container">
            <button onclick="get_list()">User Management</button>
            <button onclick="get_car_list()">Vehicle Management</button>
            <button onclick="get_motorcycle_list()">Motorcycle Management</button>
        </div>
    </div>

<!-- here we will display the list of normal users or otherwise known as customers -->
    <div id="profile_list">
        <h1 class="profile-title">Customer List</h1>
        <!-- here we create a table to get the data and display it for the users 
          -->
        <table class="client-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody id="body-profile-list">
<!-- here we will get the data and display it through some js logic that we have in profile.js
 populatetable -->
        </tbody>
        </table>
    </div>

    <!-- here we will do the same logic but this time is for the user details 
     so we make a tbl and through profile.js we fetch the data of specific customers -->
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
<!-- here we will get the data read and fill the table dynamically  -->
            </tbody>
            </table>
        </div>

<!-- in this section we make it so we show it only to non admins so if the role is admin u dont get to see this section 
  -->
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
<!--here this part is empty at load time but gets filled by JavaScript -->
            </tbody>
            </table>
        </div>

<!-- here its the vechile list the admin gets  -->
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
        <!-- the section for the motorcycle list -->
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

        <!-- here we will make a basic form to collect the car fields and then we go to 
         profile.js where we run the function submitNewCar  which will validate the input 
         ten build a FormData payload and will send a POST request using AJAX to the lets
         say backend part which is admin_addCar.php-->
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

<!-- here we have almost the same method we get the info here in the form then send it over
 to submitNewCar which forms the payload and makes a post request through ajax 
 and the data gets validates then its placed on the db  -->
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

        
        <!-- Modal: car detail view -->
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
        <!-- Modal: motorcycle detail view -->
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
        <!-- Modal: booking history -->
        <div id="bookingHistoryModal" class="modal">
            <div id="bokingHistory" class="bokingHistory">
                <h2>Booking history</h2>
                <div id="bokingHistoryContainer"></div>
                <button id="exitButton">Exit</button>
            </div>
        </div>
        

</body>
<!-- Car/motorcycle detail modals -->
<script src="../makinat/carDetails.js"></script>
<script src="./motorcycleDetails.js"></script>

<!-- Profile page behavior -->
<script src="profile.js"></script>
<!-- Session timeout handling -->
<script src="../registerLogin/sessionTimeout.js"></script>




<script>
    
</script>


</html>
