let motorcycleImagePaths;
let motorcycleImagePosition = 0;
let motorcycleImageLength = 0;
const motorcycleTransmissionLabels = { automatik: "Automatic", manual: "Manual" };

function normalizeMotorcycleLabel(value, map) {
    if (!value) return value;
    const key = value.toString().toLowerCase();
    return map[key] || value;
}

function getMotorcycleCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
}

function showMotorcycleDetails() {
    const clickedElement = event.target.closest('.container');
    const motorcycleId = clickedElement.id;
    document.cookie = "motorcycle_id=" + motorcycleId + "; path=/";

    motorcycleImagePosition = 0;
    document.getElementById("motorcycleDetailsModal").style.display = 'block';
    if (motorcycleId) {
        const data = {
            carId: motorcycleId,
            action: "getCarDetails"
        };

        fetch("motorcycleDetails.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("motorcycleName").innerText = data.name;
                document.getElementById("motorcyclePrice").innerText = data.pricePerDay;
                document.getElementById("motorcycleEngineCc").innerText = data.engineCc;
                document.getElementById("motorcycleTransmission").innerText = normalizeMotorcycleLabel(data.transmission, motorcycleTransmissionLabels);
                document.getElementById("motorcycleSeatHeight").innerText = data.seatHeightMm;
                document.getElementById("motorcycleWeight").innerText = data.weightKg;
                document.getElementById("motorcycleAbs").innerText = data.abs == 1 ? "Yes" : "No";
                document.getElementById("motorcycleFuel").innerText = data.fuel;
                document.getElementById("motorcycleColor").innerText = data.color;
                document.getElementById("motorcycleType").innerText = data.type;
                document.getElementById("motorcycleYear").innerText = data.year;
                motorcycleImagePaths = Array.isArray(data.images) ? data.images : [];
                motorcycleImageLength = motorcycleImagePaths.length;
                const firstImage = motorcycleImagePaths[0];
                const imageSrc = firstImage ? "/Makina/images/motorcycles/" + firstImage : "/Makina/images/default.jpg";
                document.getElementById("motorcycleImage").src = imageSrc;
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    } else {
        alert("motorcycleId not found in cookies");
    }
}

document.getElementById("motorcycleCloseButton").addEventListener("click", function() {
    document.getElementById("motorcycleDetailsModal").style.display = "none";
});

window.addEventListener("click", function(event) {
    if (event.target == document.getElementById("motorcycleDetailsModal")) {
        document.getElementById("motorcycleDetailsModal").style.display = "none";
    }
});

document.getElementById("motoLeftArrow").addEventListener('click', function() {
    if (motorcycleImageLength > 0 && motorcycleImagePosition - 1 >= 0) {
        motorcycleImagePosition--;
        document.getElementById("motorcycleImage").src = "/Makina/images/motorcycles/" + motorcycleImagePaths[motorcycleImagePosition];
    }
});

document.getElementById("motoRightArrow").addEventListener('click', function() {
    if (motorcycleImageLength > 0 && motorcycleImagePosition + 1 < motorcycleImageLength) {
        motorcycleImagePosition++;
        document.getElementById("motorcycleImage").src = "/Makina/images/motorcycles/" + motorcycleImagePaths[motorcycleImagePosition];
    }
});

document.getElementById("motorcycleDeleteButton").addEventListener("click", function() {
    const motorcycleId = getMotorcycleCookie('motorcycle_id');

    if (!motorcycleId) {
        console.log('Motorcycle ID not found in cookies');
        return;
    }

    fetch('deleteMotorcycle.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ motorcycle_id: motorcycleId })
    })
    .then(response => response.json())
    .then(data => {
        alert("Motorcycle deleted successfully");
        document.getElementById("motorcycleDetailsModal").style.display = "none";
        exit_list();
    })
    .catch(error => {
        console.error('Error deleting motorcycle:', error);
    });
});
