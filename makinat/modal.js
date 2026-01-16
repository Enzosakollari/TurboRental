var modal = document.getElementById("myModal");
var filterBtn = document.getElementById("filterBtn");
var closeBtn = document.getElementById("closeBtn");

filterBtn.onclick = function() {
    modal.style.display = "block";
}

closeBtn.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
    if (event.target == document.getElementById("carDetailsModal")) {
        document.getElementById("carDetailsModal").style.display = "none";
    }
}

document.getElementById("rezervoButton").addEventListener("click", function(){

    if(document.getElementById("startDate").value == "" || document.getElementById("endDate").value == ""){
        alert("Please fill in the reservation dates.");
        return;
    }
    console.log(document.getElementById("diteTotale").textContent);
    console.log(document.getElementById("startDate").value); 
    console.log(document.getElementById("endDate").value); 
    console.log(document.getElementById("carPrice").value);
    carId = getCookie('car_id');

    const data = {
        carId: carId,
        startDate: document.getElementById("startDate").value,
        endDate: document.getElementById("endDate").value,
        totalDays: document.getElementById("diteTotale").textContent,
        pricePerDay: document.getElementById("carPrice").innerText,
        action: "addToCart"
    };
    fetch("carDetails.php",{
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            document.getElementById("carDetailsModal").style.display = "none";
        } else {
            alert(data.message);
        }
        })
    .catch(error => {
        console.error("Error:", error);
    });
});
