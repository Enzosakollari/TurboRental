
let imagePaths;
            let imagePosition = 0;
            let imagePathLength = 0;
            const transmissionLabels = { automatik: "Automatic", manual: "Manual" };
            
            function normalizeLabel(value, map) {
                if (!value) return value;
                const key = value.toString().toLowerCase();
                return map[key] || value;
            }
            
            function getCookie(name) {
                const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                return match ? match[2] : null;
            }
            
            
            function showCarDetails(){

                const clickedElement = event.target.closest('.container');
                carId = clickedElement.id;
                document.cookie = "car_id=" + carId + "; path=/";

                console.log(carId);
                imagePosition = 0;
                document.getElementById("carDetailsModal").style.display = 'block';  
                if(carId){
                  console.log(carId);
                  
                  const data = {
                      carId: carId,
                      action: "getCarDetails"
                  };
              
                  
                  fetch("carDetails.php", {
                      method: "POST",
                      headers: {
                          "Content-Type": "application/json"
                      },
                      body: JSON.stringify(data)
                  })
                  .then(response => response.json())
                  .then(data => {
                      if(data.success){
                          
                          document.getElementById("carName").innerText = data.name;
                          document.getElementById("carPrice").innerText = data.pricePerDay;
                          document.getElementById("carSeatingCapacity").innerText = data.seatingCapacity;
                          document.getElementById("carEngine").innerText = data.engine;
                          document.getElementById("carTransmission").innerText = normalizeLabel(data.transmission, transmissionLabels);
                          if(data.bluetooth == 1){
                              document.getElementById("carBluetooth").innerText = "Yes";
                          }
                          else{
                              document.getElementById("carBluetooth").innerText = "No";
                          }
                          if(data.gps == 1){
                              document.getElementById("carGPS").innerText = "Yes";
                          }
                          else{
                              document.getElementById("carGPS").innerText = "No";
                          }
                          document.getElementById("carColor").innerText = data.color;
                          document.getElementById("carType").innerText = data.type;
                          document.getElementById("carYear").innerText = data.year;
                          imagePaths = Array.isArray(data.images) ? data.images : [];
                          console.log('Image paths:', imagePaths);
                          imagePathLength = imagePaths.length;
                          console.log(imagePathLength);
                          const firstImage = imagePaths[0];
                          const imageSrc = firstImage ? "/Makina/images/cars/" + firstImage : "/Makina/images/default.jpg";
                          document.getElementById("carImage").src = imageSrc;
              
              
                      } else {
                          alert(data.message);
                      }
                  })
                  .catch(error => {
                      console.error("Error:", error);
                  });
                }
                else{
                    alert("carId not found in cookies");
                }
              
              
                
                const date = new Date();
                document.getElementById("startDate").min = date.toISOString().split('T')[0];
                date.setDate(date.getDate() + 1);
                document.getElementById("endDate").min = date.toISOString().split('T')[0];
              
              }

              
              
              document.getElementById("dilButton").addEventListener("click", function(){
                document.getElementById("carDetailsModal").style.display = "none";

              });
              window.onclick = function(event) {
                if (event.target == document.getElementById("carDetailsModal")) {
                  document.getElementById("carDetailsModal").style.display = "none";
                }
              };
            
            document.getElementById("startDate").addEventListener("change", function(){
                const date = new Date(document.getElementById("startDate").value);
                if(document.getElementById("startDate").value == ""){
                    document.getElementById("endDate").max = "";

                    const date = new Date();
                    date.setDate(date.getDate() + 1);
                    document.getElementById("endDate").min = date.toISOString().split('T')[0];
                }
                else{
                    date.setDate(date.getDate() + 1);
                    document.getElementById("endDate").min = date.toISOString().split('T')[0];

                    date.setDate(date.getDate() + 29);
                    document.getElementById("endDate").max = date.toISOString().split('T')[0];
                }
                document.getElementById("endDate").value = '';  

                
            });

            
            function calculateRentalDays() {
                let startDate = new Date(document.getElementById("startDate").value);
                let endDate = new Date(document.getElementById("endDate").value);
                if(document.getElementById("startDate").value == "" || document.getElementById("endDate").value == ""){
                    document.getElementById("diteTotale").textContent = "";
                }
                else{
                    const diferenca = (endDate - startDate)/(1000 * 3600 * 24);
                    document.getElementById("diteTotale").textContent = diferenca;
                }

            }
            document.getElementById("startDate").addEventListener('change', calculateRentalDays);
            document.getElementById("endDate").addEventListener('change', calculateRentalDays);

            

            
            
            document.getElementById("leftArrow").addEventListener('click', function(){
                if(imagePathLength > 0 && imagePosition - 1 >= 0){
                    imagePosition--;

                    document.getElementById("carImage").src = "/Makina/images/cars/"+imagePaths[imagePosition];

                }
            });

            document.getElementById("rightArrow").addEventListener('click', function(){
                if(imagePathLength > 0 && imagePosition + 1 < imagePathLength){
                    imagePosition++;

                    document.getElementById("carImage").src = "/Makina/images/cars/"+imagePaths[imagePosition];

                }
            });

            
