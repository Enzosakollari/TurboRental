// Profile page controller: handles admin/user views and CRUD actions.
$(document).ready(function() {
    // Start with everything hidden, then show the right section after we load the user.
    toggleProfileSections({ user: false, list: false, admin: false, menueadmin: false, car: false, motorcycleList: false, addMotorcycle: false });
    get_user_main();
});

// Central switchboard for showing/hiding the major UI sections.
function toggleProfileSections({ user = false, list = false, admin = false, addUser = false, menueadmin = false, car = false, addCar = false, motorcycleList = false, addMotorcycle = false } = {}) {
    $('#profile_user').toggle(user);
    $('#profile_list').toggle(list);
    $('#profile_admin').toggle(admin);
    $('#add_user_form').toggle(addUser);
    $('#menue_admin').toggle(menueadmin);
    $('#car_list_form').toggle(car);
    $('#car_details').toggle(addCar);
    $('#motorcycle_list_form').toggle(motorcycleList);
    $('#motorcycle_details').toggle(addMotorcycle);
}

// Renders a table body from an array of objects and a field list.
function populateTable(tbodySelector, dataArray, fields) {
    const tbody = document.querySelector(tbodySelector);
   
    tbody.innerHTML = ""; 
    dataArray.forEach(item => {
        const row = document.createElement('tr');
        row.id = item.id;
        row.classList.add("container");
        fields.forEach(field => {
            const cell = document.createElement('td');
            cell.textContent = item[field] || '';
            row.appendChild(cell);
        });
        
        // Row click behavior depends on the current list we are showing.
        if (currentTableType === 'user') {
            row.addEventListener('click', () => goDETAILS(item.id)); 
        } else if (currentTableType === 'car') {
            row.addEventListener('click', () => showCarDetails()); 
        } else if (currentTableType === 'motorcycle') {
            row.addEventListener('click', () => showMotorcycleDetails());
        }
        tbody.appendChild(row);
    });
    // Footer actions per table type (admin-only).
    if (currentTableType === 'user') {
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
              
                <td colspan="3">
    <button onclick="showAddUserForm()" style="margin-right: 90px;">Add User</button>
    <button onclick="signOut()" style="margin-right: 120px;">Sign out</button>
    <button onclick="exit_list()">Exit</button>
</td>        
            </tr>
        `);
    }
    if (currentTableType === 'car') {
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
                <td colspan="3">
                    <div class="button-container">
                        <button onclick="showAddCarForm()">Add Vehicle</button>
                        <button onclick="signOut()">Sign out</button>
                        <button onclick="exit_list()">Exit</button>
                    </div>
                </td>
            </tr>
        `);
    }
    if (currentTableType === 'motorcycle') {
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
                <td colspan="3">
                    <div class="button-container">
                        <button onclick="showAddMotorcycleForm()">Add Motorcycle</button>
                        <button onclick="signOut()">Sign out</button>
                        <button onclick="exit_list()">Exit</button>
                    </div>
                </td>
            </tr>
        `);
    }
}

// Detect current user and show either admin menu or user profile.
function get_user_main() {
    $.ajax({
        url: 'get_user.php',
        method: 'GET',
        dataType: 'json',
        success: (data) => {
            if (!data || data.length === 0) {
                alert("User not found.");
                return;
            }

             role_id = data['role_id'];
             user_id = data['id'];
            if (role_id === 1) {
                toggleProfileSections({ user: false, list: false, admin: false, menueadmin: true, car: false, addCar:false, motorcycleList: false, addMotorcycle: false });
            } else {
                get_user(user_id);
                toggleProfileSections({ user: true, list: false, admin: false, menueadmin: false, car: false, addCar: false, addUser: false, motorcycleList: false, addMotorcycle: false });
            }
        },
        error: (err) => {
            console.error("Error:", err);
            alert("An error occurred while fetching user data.");
        }
    });
}

// Admin: fetch and render all users.
function get_list() {
    currentTableType = 'user';
    $.ajax({
        url: 'admin_get_client_list.php',
        method: 'GET',
        dataType: 'json',
        success: response => {
            if (!response || response.status !== "success") {
                alert("Error: " + (response ? response.message : "Unknown error"));
                return;
            }

            const data = response.data;
            if (!data || data.length === 0) {
                alert("No clients available.");
                return;
            }
            populateTable('#body-profile-list', data, ['id', 'username', 'email']);
            toggleProfileSections({ user: false, list: true, admin: false, menueadmin: false, car: false, addCar:false, motorcycleList: false, addMotorcycle: false }); // Show the list
        },
        error: err => {
            console.error("Error fetching client list:", err);
            alert("An error occurred while fetching the client list.");
        }
    });
}

// Admin: fetch and render the car list.
function get_car_list() {
    currentTableType = 'car';
    $.ajax({
        url: 'admin_get_carlist.php',
        method: 'GET',
        dataType: 'json',
        success: response => {
            if (!response || response.status !== "success") {
                alert("Error: " + (response ? response.message : "Unknown error"));
                return;
            }

            const data = response.data || [];
            if (data.length === 0) {
                alert("No cars available.");
            }
            populateTable('#body-profile-details-cars-list', data, ['name', 'price_per_day', 'type']);
            toggleProfileSections({ user: false, list: false, admin: false, menueadmin: false, car: true, addCar:false, motorcycleList: false, addMotorcycle: false }); // Show the car list
        },
        error: err => {
            console.error("Error fetching car list:", err);
            alert("An error occurred while fetching the car list.");
        }
    });
}

// Admin: fetch and render the motorcycle list.
function get_motorcycle_list() {
    currentTableType = 'motorcycle';
    $.ajax({
        url: 'admin_get_motorcycle_list.php',
        method: 'GET',
        dataType: 'json',
        success: response => {
            if (!response || response.status !== "success") {
                alert("Error: " + (response ? response.message : "Unknown error"));
                return;
            }

            const data = response.data || [];
            if (data.length === 0) {
                alert("No motorcycles available.");
            }
            populateTable('#body-profile-details-motorcycle-list', data, ['name', 'price_per_day', 'type']);
            toggleProfileSections({ user: false, list: false, admin: false, menueadmin: false, car: false, addCar: false, motorcycleList: true, addMotorcycle: false });
        },
        error: err => {
            console.error("Error fetching motorcycle list:", err);
            alert("An error occurred while fetching the motorcycle list.");
        }
    });
}

// Regular user: load their own profile details.
function get_user(userId) {
    $.ajax({
        url: 'admin_get_client_select.php',
        method: 'GET',
        data: { id: userId },
        dataType: 'json',
        success: client => {
            populateDetails('#body-profile-details-user', client);
            $('#profile_user').show();
        },
        error: err => {
            console.error("Error fetching user details:", err);
            alert("An error occurred while fetching user details.");
        }
    });
}

// Admin: view details for a specific user.
function goDETAILS(userId) {
    $.ajax({
        url: 'admin_get_client_select.php',
        data: { id: userId },
        method: 'GET',
        dataType: 'json',
	success: user => {
    		populateDetails('#body-profile-details', user);
    		$('#profile_admin').show();
        },
        error: function (err) {
            console.error("Error fetching client details:", err);
            alert("An error occurred while fetching client details.");
        }
    });
    
}

// Admin: build and show the "Add User" form dynamically.
function showAddUserForm() {
    toggleProfileSections({ user: false, list: false, admin: false,car:false,menueadmin:false, addUser: true,addCar:false});
    const formContainer = document.createElement('div');
    formContainer.id = 'add_user_form';
    formContainer.innerHTML = `
        <h2 id="add_user_title">Add User</h2>
<table>
    <tr>
        <td><input type="text" id="user_name_new" placeholder="Username"></td>
    </tr>
    <tr>
        <td><input type="text" id="user_fullname_new" placeholder="Full name"></td>
    </tr>
    <tr>
        <td><input type="text" id="user_address_new" placeholder="Address"></td>
    </tr>
    <tr>
        <td><input type="tel" id="user_phone_number_new" placeholder="Phone number"></td>
    </tr>
    <tr>
        <td><input type="text" id="user_email_new" placeholder="Email"></td>
    </tr>
    <tr>
        <td><input type="password" id="user_password_new" placeholder="Password"></td>
    </tr>
    <tr>
        <td><input type="number" id="user_role_new" placeholder="Role ID"></td>
    </tr>
    <tr>
        <td><input type="checkbox" id="user_verified_new"> Verified</td>
    </tr>
    <tr>
            <td>
                    <label for="user_profile_image_new" class="custom-file-upload">Choose Profile Photo</label>
                    <input type="file" id="user_profile_image_new" accept="image/*" style="display: none;">
                </td>    
    </tr>
    <tr>
        <td>
            <button onclick="submitNewUser()">Submit</button>
            <button onclick="cancelAddUser()">Cancel</button>
        </td>
    </tr>
</table>
    `;
    document.body.appendChild(formContainer);
}

// Admin: show the "Add Car" form (already in the DOM).
function showAddCarForm(){
    toggleProfileSections({ user: false, list: false, admin: false, addUser: false, menueadmin: false, car: false, addCar: true, motorcycleList: false, addMotorcycle: false });

}

// Admin: show the "Add Motorcycle" form (already in the DOM).
function showAddMotorcycleForm(){
    toggleProfileSections({ user: false, list: false, admin: false, addUser: false, menueadmin: false, car: false, addCar: false, motorcycleList: false, addMotorcycle: true });
}

// Admin: hide the add-user form and go back to the list.
function cancelAddUser() {
    toggleProfileSections({ user: false, list: true, admin: false, addUser: false, addCar:false, menueadmin:false, car:false, motorcycleList: false, addMotorcycle: false });
    document.querySelector('#add_user_form').remove();
}

// Admin: return from add-car to the car list.
function cancelAddCar() {
        toggleProfileSections({ user: false, list: false, admin: false, addUser: false, menueadmin: false, car: true, addCar:false, motorcycleList: false, addMotorcycle: false });
}

// Admin: return from add-motorcycle to the motorcycle list.
function cancelAddMotorcycle() {
        toggleProfileSections({ user: false, list: false, admin: false, addUser: false, menueadmin: false, car: false, addCar:false, motorcycleList: true, addMotorcycle: false });
}

// Admin: validate the form and send the new user to the server.
function submitNewUser() {
    const usernameElement = document.querySelector('#user_name_new');
    const emailElement = document.querySelector('#user_email_new');
    const fullnameElement= document.querySelector('#user_fullname_new');
    const addressElement= document.querySelector('#user_address_new');
    const telephoneElement= document.querySelector('#user_phone_number_new');
    const passwordElement = document.querySelector('#user_password_new');
    const roleElement = document.querySelector('#user_role_new');
    const verifiedElement = document.querySelector('#user_verified_new');
    const fileInput = document.querySelector('#user_profile_image_new');

    // Basic client-side validation.
    if (!usernameElement.value.trim() || !fullnameElement.value.trim() || !addressElement.value.trim() || !telephoneElement.value.trim() ||
     !emailElement.value.trim() || !passwordElement.value.trim() || !roleElement.value.trim()) {
        alert("Please fill in all required fields.");
        return;
    }
     const roleValue = parseInt(roleElement.value.trim(), 10);
     if (roleValue !== 1 && roleValue !== 2) {
         alert("User role must be either 1 or 2.");
         return;
     }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailElement.value.trim())) {
        alert("Please enter a valid email address.");
        return;
    }
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*[-._!#$%&?])[A-Za-z\d!#$%&?]{8,255}$/;
    if (!passwordRegex.test(passwordElement.value.trim())) {
        alert("Password must be at least 8 characters long and include a letter and a special character.");
        return;
    }
    const newEmail = emailElement.value.trim();

    // Check if email is already used before submitting the full form.
    $.ajax({
        url: 'check_email.php',
        method: 'POST',
        data: { email: newEmail },
        dataType: 'json',
        success: response => {
            if (response.exists) {
                alert("This email is already in use. Please use a different email.");
            } else {
                const newName = usernameElement.value;
                const newPassword = passwordElement.value;
                const newRole = roleElement.value;
                const newIsVerified = verifiedElement.checked ? 1 : 0;
                const newFullname= fullnameElement.value;
                const newAddress= addressElement.value;
                const newTelephone= telephoneElement.value;

                // Use FormData so we can send the optional profile image.
                const formData = new FormData();
                formData.append('username', newName);
                formData.append('email', newEmail);
                formData.append('password', newPassword);
                formData.append('role_id', newRole);
                formData.append('is_verified', newIsVerified);
                formData.append('fullname', newFullname);
                formData.append('address', newAddress);
                formData.append('telephone', newTelephone);
               
                if (fileInput.files.length > 0) {
                    formData.append('profile_picture', fileInput.files[0]);
                }
                $.ajax({
                    url: 'admin_addUser.php',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: response => {
                        console.log(response);
                        alert('User added successfully!');
                        toggleProfileSections({ user: false, list: true, admin: false, addUser: false,menueadmin:false,addCar:false });
                        document.querySelector('#add_user_form').remove();
                        get_list();
                    },
                    error: err => {
                        console.error("Error adding user:", err);
                        alert("An error occurred while adding the user.");
                    }
                });
            }
        },
        error: err => {
            console.error("Error checking email:", err);
            alert("An error occurred while checking the email.");
        }
    });
}

// Admin: validate and submit a new car.
function submitNewCar() {
    const nameElement = document.querySelector('#name_new');
    const priceElement = document.querySelector('#price_per_day_new');
    const fuelElement = document.querySelector('#fuel_new');
    const seatsElement = document.querySelector('#seating_capacity_new');
    const engineElement = document.querySelector('#engine_new');
    const transmissionElement = document.querySelector('#transmission_new');
    const yearElement = document.querySelector('#year_new');
    const bluetoothElement = document.querySelector('#bluetooth_new');
    const gpsElement = document.querySelector('#gps_new');
    const colorElement = document.querySelector('#color_new');
    const typeElement = document.querySelector('#type_new');
    const fileInput = document.querySelector('#profile_pictures_new');

    // Basic client-side validation.
    if (!nameElement.value.trim() || !priceElement.value.trim() || !fuelElement.value.trim() || !seatsElement.value.trim() ||
        !engineElement.value.trim() || !transmissionElement.value.trim() || !yearElement.value.trim() || !colorElement.value.trim() || !typeElement.value.trim()) {
        alert("Please fill in all required fields.");
        return;
    }
    const formData = new FormData();
    formData.append('name', nameElement.value);
    formData.append('price_per_day', priceElement.value);
    formData.append('fuel', fuelElement.value);
    formData.append('seating_capacity', seatsElement.value);
    formData.append('engine', engineElement.value);
    formData.append('transmission', transmissionElement.value);
    formData.append('year', yearElement.value);
    formData.append('bluetooth', bluetoothElement.checked ? 1 : 0);
    formData.append('gps', gpsElement.checked ? 1 : 0);
    formData.append('color', colorElement.value);
    formData.append('type', typeElement.value);

    // Attach all selected photos.
    if (fileInput.files.length > 0) {
        for (let i = 0; i < fileInput.files.length; i++) {
            formData.append('profile_pictures[]', fileInput.files[i]);
        }
    }
    $.ajax({
        url: 'admin_addCar.php',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: response => {
            console.log(response);
            alert('Car added successfully!');
            toggleProfileSections({ user: false, list: false, admin: false, addUser: false, menueadmin: false, car: true, addCar: false, motorcycleList: false, addMotorcycle: false });
            get_car_list();
        },
        error: err => {
            console.error("Error adding car:", err);
            alert("An error occurred while adding the car.");
        }
    });
}

// Admin: validate and submit a new motorcycle.
function submitNewMotorcycle() {
    const nameElement = document.querySelector('#moto_name_new');
    const priceElement = document.querySelector('#moto_price_per_day_new');
    const fuelElement = document.querySelector('#moto_fuel_new');
    const engineElement = document.querySelector('#moto_engine_cc_new');
    const transmissionElement = document.querySelector('#moto_transmission_new');
    const yearElement = document.querySelector('#moto_year_new');
    const absElement = document.querySelector('#moto_abs_new');
    const colorElement = document.querySelector('#moto_color_new');
    const typeElement = document.querySelector('#moto_type_new');
    const weightElement = document.querySelector('#moto_weight_kg_new');
    const seatHeightElement = document.querySelector('#moto_seat_height_mm_new');
    const fileInput = document.querySelector('#moto_pictures_new');

    // Basic client-side validation.
    if (!nameElement.value.trim() || !priceElement.value.trim() || !fuelElement.value.trim() ||
        !engineElement.value.trim() || !transmissionElement.value.trim() || !yearElement.value.trim() ||
        !colorElement.value.trim() || !typeElement.value.trim() || !weightElement.value.trim() || !seatHeightElement.value.trim()) {
        alert("Please fill in all required fields.");
        return;
    }

    const formData = new FormData();
    formData.append('name', nameElement.value);
    formData.append('price_per_day', priceElement.value);
    formData.append('fuel', fuelElement.value);
    formData.append('engine_cc', engineElement.value);
    formData.append('transmission', transmissionElement.value);
    formData.append('year', yearElement.value);
    formData.append('abs', absElement.checked ? 1 : 0);
    formData.append('color', colorElement.value);
    formData.append('type', typeElement.value);
    formData.append('weight_kg', weightElement.value);
    formData.append('seat_height_mm', seatHeightElement.value);

    // Attach all selected photos.
    if (fileInput.files.length > 0) {
        for (let i = 0; i < fileInput.files.length; i++) {
            formData.append('moto_pictures[]', fileInput.files[i]);
        }
    }

    $.ajax({
        url: 'admin_addMotorcycle.php',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: response => {
            console.log(response);
            alert('Motorcycle added successfully!');
            toggleProfileSections({ user: false, list: false, admin: false, addUser: false, menueadmin: false, car: false, addCar: false, motorcycleList: true, addMotorcycle: false });
            get_motorcycle_list();
        },
        error: err => {
            console.error("Error adding motorcycle:", err);
            alert("An error occurred while adding the motorcycle.");
        }
    });
}
// Admin: close the details pane and return to the list.
function exit_admin() {
    $('#profile_admin').hide();
        $('#profile_user').hide();
    $('#profile_list').show();
    console.log("Exited admin profile view.");
}
// Admin: return to the main admin menu.
function exit_list(){
    toggleProfileSections({user: false, list: false, admin: false, addUser: false, menueadmin: true, car: false, addCar: false, motorcycleList: false, addMotorcycle: false});
}

// Build a table of user details (admin or user view).
function populateDetails(tbodySelector, user) {
    const tbody = document.querySelector(tbodySelector);
    tbody.innerHTML = ""; 

    let rowId = document.createElement('tr');
    
    let thId = document.createElement('td');
    thId.textContent = 'ID';
    
     // Admin-only: show the numeric user ID.
     if(role_id == 1){
        let tdIdValue = document.createElement('td');
        let inputId = document.createElement('input');
        inputId.type = 'text';
        inputId.value = user.id;
        inputId.readOnly = true; 
    
        tdIdValue.appendChild(inputId);
        rowId.appendChild(thId);
        rowId.appendChild(tdIdValue);
        tbody.appendChild(rowId);

     }

    let rowName = document.createElement('tr');
    
    let thName = document.createElement('td');
    thName.textContent = 'Username';
    
    let tdNameValue = document.createElement('td');
    let inputName = document.createElement('input');
    inputName.type = 'text';
    inputName.value = user.username;
    inputName.id = 'user_name_' + user.id; 
    
    tdNameValue.appendChild(inputName);
    rowName.appendChild(thName);
    rowName.appendChild(tdNameValue);
    tbody.appendChild(rowName);

    let rowEmriPlote = document.createElement('tr');
    
    let thEmriPlote = document.createElement('td');
    thEmriPlote.textContent = 'Full name';
    
    let tdEmriPloteValue = document.createElement('td');
    let inputEmriPlote = document.createElement('input');
    inputEmriPlote.type = 'text';
    inputEmriPlote.value = user.full_name;
    inputEmriPlote.id = 'emri_plote_' + user.id;
    
    tdEmriPloteValue.appendChild(inputEmriPlote);
    rowEmriPlote.appendChild(thEmriPlote);
    rowEmriPlote.appendChild(tdEmriPloteValue);
    tbody.appendChild(rowEmriPlote);

    let rowAddress = document.createElement('tr');
    
    let thAddress = document.createElement('td');
    thAddress.textContent = 'Address';
    
    let tdAddressValue = document.createElement('td');
    let inputAddress = document.createElement('input');
    inputAddress.type = 'text';
    inputAddress.value = user.address;
    inputAddress.id = 'adresa' + user.id;
    
    tdAddressValue.appendChild(inputAddress);
    rowAddress.appendChild(thAddress);
    rowAddress.appendChild(tdAddressValue);
    tbody.appendChild(rowAddress);

    let rowtel = document.createElement('tr');
    
    let thtel = document.createElement('td');
    thtel.textContent = 'Phone numbert';
    
    let tdtelValue = document.createElement('td');
    let inputtel = document.createElement('input');
    inputtel.type = 'tel';
    inputtel.value = user.phone_number;
    inputtel.id = 'tel' + user.id; 
    
    tdtelValue.appendChild(inputtel);
    rowtel.appendChild(thtel);
    rowtel.appendChild(tdtelValue);
    tbody.appendChild(rowtel);

    let rowEmail = document.createElement('tr');
    
    let thEmail = document.createElement('td');
    thEmail.textContent = 'Email';
    
    let tdEmailValue = document.createElement('td');
    let inputEmail = document.createElement('input');
    inputEmail.type = 'text'; 
    inputEmail.value = user.email;
    inputEmail.id = 'user_email_' + user.id;
    
    tdEmailValue.appendChild(inputEmail);
    rowEmail.appendChild(thEmail);
    rowEmail.appendChild(tdEmailValue);
    tbody.appendChild(rowEmail);
    
    // Admin-only: show password field (empty for manual reset).
    if(role_id==1){
    let rowPassword = document.createElement('tr');
    
    let thPassword = document.createElement('td');
    thPassword.textContent = 'Password';
    
    let tdPasswordValue = document.createElement('td');
    let inputPassword = document.createElement('input');
    inputPassword.type = 'password';
    inputPassword.id = 'user_password_' + user.id; // or some unique ID
    
    tdPasswordValue.appendChild(inputPassword);
    rowPassword.appendChild(thPassword);
    rowPassword.appendChild(tdPasswordValue);
    tbody.appendChild(rowPassword);
}
  
    let rowProfile = document.createElement('tr');

let thProfile = document.createElement('td');
thProfile.textContent = 'Profile Photo';

let tdProfileValue = document.createElement('td');

let imgElement = document.createElement('img');
let baseImageUrl = '/Makina/images/';
let profileImageName = user.profile_image ? user.profile_image : 'profileImage.jpg';
let imagePath = baseImageUrl + profileImageName;

imgElement.src = imagePath;
imgElement.alt = "User Profile Image";
imgElement.style.maxWidth = '100px';

console.log("Image path:", imagePath);

imgElement.onerror = () => {
    console.error("Image not found:", imagePath);
};

let fileInput = document.createElement('input');
fileInput.type = 'file';
fileInput.accept = 'image/*'; 
fileInput.id = 'user_profile_image_' + user.id;
fileInput.style.display = 'none'; 

let fileInputLabel = document.createElement('label');
fileInputLabel.htmlFor = fileInput.id;
fileInputLabel.textContent = 'Add Profile Photo';
fileInputLabel.className = 'custom-file-upload';

fileInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        imgElement.src = e.target.result; 
    };
    reader.readAsDataURL(file);
});

tdProfileValue.appendChild(imgElement);
tdProfileValue.appendChild(document.createElement('br'));
tdProfileValue.appendChild(fileInputLabel);
tdProfileValue.appendChild(fileInput);
rowProfile.appendChild(thProfile);
rowProfile.appendChild(tdProfileValue);
tbody.appendChild(rowProfile);
	
    // Admin-only: show role, verified status, and timestamps.
    if(role_id == 1){
        let rowRole = document.createElement('tr');
        let thRole = document.createElement('td');
        thRole.textContent = 'Role ID';
        let tdRoleValue = document.createElement('td');
        let inputRole = document.createElement('input');
        inputRole.type = 'number';
        inputRole.value = user.role_id;
        inputRole.id = 'user_role_' + user.id;
        tdRoleValue.appendChild(inputRole);
        rowRole.appendChild(thRole);
        rowRole.appendChild(tdRoleValue);
        tbody.appendChild(rowRole);

	let rowVerified = document.createElement('tr');
	let thVerified = document.createElement('td');
	thVerified.textContent = 'Is Verified';
	let tdVerifiedValue = document.createElement('td');
	let inputVerified = document.createElement('input');
	inputVerified.type= 'checkbox';
	inputVerified.checked= !!user.is_verified;
	inputVerified.id= 'user_verified_' + user.id;
   
	tdVerifiedValue.appendChild(inputVerified);
	rowVerified.appendChild(thVerified);
	rowVerified.appendChild(tdVerifiedValue);
	tbody.appendChild(rowVerified);

	let rowCreated= document.createElement('tr');
	let thCreated= document.createElement('td');
	thCreated.textContent= 'Created At';
	let tdCreatedValue= document.createElement('td');
	let inputCreated= document.createElement('input');
	inputCreated.type= 'text';
	inputCreated.value= user.created_at;
	inputCreated.readOnly= true; 
	tdCreatedValue.appendChild(inputCreated);
	rowCreated.appendChild(thCreated);
	rowCreated.appendChild(tdCreatedValue);
	tbody.appendChild(rowCreated);

	let rowUpdated= document.createElement('tr');
	let thUpdated= document.createElement('td');
	thUpdated.textContent= 'Updated At';
	let tdUpdatedValue= document.createElement('td');
	let inputUpdated= document.createElement('input');
	inputUpdated.type= 'text';
	inputUpdated.value= user.updated_at;
	inputUpdated.readOnly= true; 
	tdUpdatedValue.appendChild(inputUpdated);
	rowUpdated.appendChild(thUpdated);
	rowUpdated.appendChild(tdUpdatedValue);
	tbody.appendChild(rowUpdated);

    // Admin-only: quick access to booking history.
    let rowButton = document.createElement('tr');
    let tdButton = document.createElement('td');
    tdButton.setAttribute('colspan', '2');
    let button = document.createElement('button');
    button.textContent = 'View history';
    button.onclick = function () {
        showBookingHistory(user.id);
    };
    tdButton.appendChild(button);
    rowButton.appendChild(tdButton);
    tbody.appendChild(rowButton);

     }
     console.log(role_id);
     // Admin actions: exit, update, delete.
     if(role_id==1){

     tbody.insertAdjacentHTML('beforeend', `
        <tr>
          <td colspan="2">
          
            <img id="picture3" src="/Makina/images/exit.png" 
                 style="height:50px; width:50px; float:left;" 
                 onclick="exit_admin();">
            </td>   
            <td colspan="2">
                <img id="picture5" src="/Makina/images/edit-user.png" 
                    style="height:50px; width:50px; float:right;" 
                    onclick="updateDETAILS(${user.id});">
            </td>
            <td colspan="2">
                <img id="picture4" src="/Makina/images/delete.png" 
                    style="height:50px; width:50px; float:right;" 
                    onclick="deleteUser(${user.id});">
            </td>
        </tr>
      `);
    }else{
        // User actions: update own data, booking history, and logout.
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
                <td colspan="1">
                    <button onclick="changePassword(${user.id})">Change Password</button>
                </td>
                <td colspan="1">
                    <button onclick="updateUser(${user.id})">Update Details</button>
                </td>      
            </tr>
            <tr>
                <td colspan="1">
                    <button onclick="showBookingHistory(${user.id})">View history</button>  
                </td>
                <td colspan="1">
                    <button onclick="signOut()">Sign out</button>  
                </td>
          `);
    }
}

// End the session server-side and return to home.
function signOut() {
    $.ajax({
        url: 'logout.php',         
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log('Logout response:', response);
            window.location.href = '/Makina/navigation/index.php'; 
        },
        error: function(err) {
            console.error('Logout error:', err);
            alert("An error occurred while logging out.");
        }
    });
}

// Admin: update a user's profile details (includes role/verified).
function updateDETAILS(userId) {
    console.log('userId:', userId);
    const usernameElement = document.querySelector('#user_name_' + userId);
    const emailElement = document.querySelector('#user_email_' + userId);
    const passwordElement = document.querySelector('#user_password_' + userId);
    const emriploteElement = document.querySelector('#emri_plote_'+ userId);
    const adresaElement=document.querySelector('#adresa'+ userId);
    const telElement= document.querySelector('#tel'+ userId);
    const roleElement = document.querySelector('#user_role_' + userId);
    const verifiedElement = document.querySelector('#user_verified_' + userId);
    const fileInput = document.querySelector('#user_profile_image_' + userId);

    // Ensure the expected inputs exist before submitting.
    if (!usernameElement || !emailElement || !passwordElement  || !emriploteElement  || !adresaElement  || !telElement  || !roleElement || !verifiedElement || !fileInput) {
        console.error("One or more elements are missing.");
        alert("An error occurred: One or more elements are missing.");
        return;
    }
    const roleValue = parseInt(roleElement.value.trim(), 10);
    if (roleValue !== 1 && roleValue !== 2) {
        alert("User role must be either 1 or 2.");
        return;
    }

   const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
   if (!emailPattern.test(emailElement.value.trim())) {
       alert("Please enter a valid email address.");
       return;
   }
//    const passwordRegex = /^(?=.[A-Za-z])(?=.[-._!#$%&?])[A-Za-z\d!#$%&?]{8,255}$/;
//    if (!passwordRegex.test(passwordElement.value.trim())) {
//        alert("Password must be at least 8 characters long and include a letter and a special character.");
//        return;
//    }
    const updatedName = usernameElement.value;
    const updatedEmail = emailElement.value;
    const updatedPassword = passwordElement.value;
    const updatedEmriPlote= emriploteElement.value;
    const updatedAddress= adresaElement.value;
    const updatedTel=telElement.value;
    const updatedRole = roleElement.value;
    const updatedIsVerified = verifiedElement.checked ? 1 : 0;

    // Use FormData so profile image can be uploaded.
    const formData = new FormData();
    formData.append('id', userId);//celesat:id,username etj perdoren ne php per te marre vlerat
    formData.append('username', updatedName);
    formData.append('email', updatedEmail);
    formData.append('emriplote', updatedEmriPlote);
    formData.append('adresa', updatedAddress);
    formData.append('nrtelefoni', updatedTel);
    
    formData.append('password', updatedPassword);
    formData.append('role_id', updatedRole);
    formData.append('is_verified', updatedIsVerified);

    if (fileInput.files.length > 0) {
        formData.append('profile_picture', fileInput.files[0]);
    }
    $.ajax({
        url: 'user_update_profile.php',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: response => {
            console.log(response);
            alert('Profile updated successfully!');
            get_list();
        },
        error: err => {
            console.error("Error updating profile:", err);
            alert("An error occurred while updating the profile.");
        }
    });
}

// User: update only their own details (no role/verified).
function updateUser(userId) {
    console.log('userId:', userId);
    const usernameElement = document.querySelector('#user_name_' + userId);
    const emailElement = document.querySelector('#user_email_' + userId);
    const passwordElement = document.querySelector('#user_password_' + userId);
    const emriploteElement = document.querySelector('#emri_plote_'+ userId);
    const adresaElement=document.querySelector('#adresa'+ userId);
    const telElement= document.querySelector('#tel'+ userId);

    const fileInput = document.querySelector('#user_profile_image_' + userId);
    // Ensure the expected inputs exist before submitting.
    if (!usernameElement || !emailElement || !emriploteElement  || !adresaElement  || !telElement || !fileInput) {
        console.error("One or more elements are missing.");
        alert("An error occurred: One or more elements are missing.");
        return;
    }
    const updatedName = usernameElement.value;
    const updatedEmail = emailElement.value;
    const updatedEmriPlote= emriploteElement.value;
    const updatedAddress= adresaElement.value;
    const updatedTel=telElement.value;

    // Use FormData so profile image can be uploaded.
    const formData = new FormData();
    formData.append('id', userId);
    formData.append('username', updatedName);
    formData.append('email', updatedEmail);
    formData.append('emriplote', updatedEmriPlote);
    formData.append('adresa', updatedAddress);
    formData.append('nrtelefoni', updatedTel);
    
    if (fileInput.files.length > 0) {
        formData.append('profile_picture', fileInput.files[0]);
    }
    $.ajax({
        url: 'user_update_profile.php',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: response => {
            console.log(response);
            alert('User profile updated successfully!');
        },
        error: err => {
            console.error("Error updating profile:", err);
            alert("An error occurred while updating the profile.");
        }
    });
}

// Admin: delete a user after confirmation.
function deleteUser(userId) {
    console.log("Deleting user with ID: ", userId); 
    if (!confirm("Are you sure you want to delete user with ID: " + userId + "?")) {
        return;
    }
    fetch('delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('User deleted successfully.');
            get_list();
            toggleProfileSections({ user: false, list: true, admin: false });
        } else {
            alert('Failed to delete user: ' + data.message);
        }
    })
    .catch(err => {
        console.error("Error deleting user:", err);
        alert("An error occurred while deleting the user.");
    });
}

// Small helper to read a cookie by name.
function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
}

// Admin: delete the currently selected car from the details modal.
document.getElementById("fshiButton").addEventListener("click", function(){
    var carId = getCookie('car_id');

    if (carId) {
    fetch('deleteCar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json', 
        },
        body: JSON.stringify({ car_id: carId }) 
    })
    .then(response => response.json()) 
    .then(data => {
        alert("Vehicle deleted successfully");
        document.getElementById("carDetailsModal").style.display = "none";
        exit_list();
    })
    .catch(error => {
        console.error('Error fetching the PHP file:', error); 
    });
    } else {
        console.log('Car ID not found in cookies');
    }
});

// User: start the password change flow by emailing a verification code.
function changePassword(){
    event.preventDefault();
    const button = event.target;
    button.disabled = true;
    fetch('/Makina/registerLogin/registerLogin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: JSON.stringify({action: "sendVerificationCode"})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data.message);
            window.location.href = "/Makina/registerLogin/confirmResetPassword.html";

        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while sending the code by email.");
    })
    .finally(() => {
        button.disabled = false;
    });

}

// User/Admin: open booking history modal and load confirmed bookings.
function showBookingHistory(userId) {
    let historyModal = document.getElementById('bookingHistoryModal');

    let bookingHistoryModule = document.getElementById('bokingHistoryContainer');

    historyModal.style.display = 'flex';

    bookingHistoryModule.innerHTML = '<p>Loading...</p>';

    fetch('getBookingHistory.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json', 
        },
        body: JSON.stringify({ userId: userId }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bookingHistoryModule.innerHTML = '';
                const table = document.createElement('table');
                table.border = 1;
                table.style.width = '100%';
                const thead = document.createElement('thead');
                thead.innerHTML = `
                    <tr>
                        <th>Car Name</th>
                        <th>Total Price</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                `;
                table.appendChild(thead);
                const tbody = document.createElement('tbody');

                data.bookings.forEach(booking => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${booking.car_name}</td>
                        <td>${booking.total_price} EUR</td>
                        <td>${booking.start_date}</td>
                        <td>${booking.end_date}</td>
                    `;
                    tbody.appendChild(row);
                });
                table.appendChild(tbody);
                bookingHistoryModule.appendChild(table);
                
            } else {
                bookingHistoryModule.innerHTML = `<p>Empty</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching booking history:', error);
            bookingHistoryModule.innerHTML = '<p>Failed to load booking history. Please try again later.</p>';
        });
}

// Close the booking history modal.
document.getElementById("exitButton").addEventListener("click", function(){
    document.getElementById('bookingHistoryModal').style.display = 'none';
    document.getElementById('bokingHistoryContainer').innerHTML = '';

});

// Click outside the modal to close it.
window.onclick = function(event) {
    if (event.target == document.getElementById("bookingHistoryModal")) {
      document.getElementById("bookingHistoryModal").style.display = "none";
      document.getElementById('bokingHistoryContainer').innerHTML = '';

    }
  };


