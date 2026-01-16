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

            function safeJsonParse(text) {
                try {
                    return JSON.parse(text);
                } catch (error) {
                    console.error("Invalid JSON response:", text);
                    return { success: false, message: "Server error. Please try again." };
                }
            }

            function buildStars(rating) {
                const total = 5;
                const filled = Math.max(0, Math.min(total, Number(rating)));
                const empty = total - filled;

                const wrapper = document.createElement("div");
                wrapper.className = "review-stars";

                const filledSpan = document.createElement("span");
                filledSpan.className = "filled";
                filledSpan.textContent = "★".repeat(filled);

                const emptySpan = document.createElement("span");
                emptySpan.className = "empty";
                emptySpan.textContent = "★".repeat(empty);

                wrapper.appendChild(filledSpan);
                wrapper.appendChild(emptySpan);
                return wrapper;
            }

            function updateReviewUI(summary, reviews, gate) {
                const summaryEl = document.getElementById("reviewSummary");
                const listEl = document.getElementById("reviewsList");
                const formEl = document.getElementById("reviewForm");
                const noticeEl = document.getElementById("reviewNotice");

                if (!summaryEl || !listEl || !formEl || !noticeEl) {
                    return;
                }

                const total = summary && typeof summary.total === "number" ? summary.total : 0;
                const average = summary && summary.average !== null ? summary.average : null;

                if (total > 0 && average !== null) {
                    summaryEl.textContent = `Average rating: ${average}/5 from ${total} review(s).`;
                } else {
                    summaryEl.textContent = "No reviews yet.";
                }

                listEl.innerHTML = "";
                if (Array.isArray(reviews)) {
                    reviews.forEach((review) => {
                        const item = document.createElement("div");
                        item.className = "review-item";

                        const meta = document.createElement("div");
                        meta.className = "review-meta";
                        const username = review.username ? review.username : "Anonymous";
                        const dateText = review.created_at ? review.created_at : "";
                        meta.textContent = `${username} - ${dateText}`;

                        const stars = buildStars(review.rating);

                        const text = document.createElement("div");
                        text.textContent = review.review_text || "";

                        item.appendChild(meta);
                        item.appendChild(stars);
                        item.appendChild(text);
                        listEl.appendChild(item);
                    });
                }

                if (gate && gate.canReview) {
                    formEl.style.display = "flex";
                    noticeEl.textContent = "";
                } else {
                    formEl.style.display = "none";
                    noticeEl.textContent = gate && gate.message ? gate.message : "Reviews are unavailable.";
                }
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
              
                  fetch("/Makina/motorcycles/carDetails.php", {
                      method: "POST",
                      headers: {
                          "Content-Type": "application/json"
                      },
                      body: JSON.stringify(data)
                  })
                  .then(response => response.text())
                  .then(text => {
                      const data = safeJsonParse(text);
                      if(data.success){
                          document.getElementById("carName").innerText = data.name;
                          document.getElementById("carPrice").innerText = data.pricePerDay;
                          document.getElementById("carSeatingCapacity").innerText = data.seatHeightMm;
                          document.getElementById("carEngine").innerText = data.engineCc;
                          document.getElementById("carTransmission").innerText = normalizeLabel(data.transmission, transmissionLabels);
                          const weightLabel = document.getElementById("carWeight");
                          if (weightLabel) {
                              weightLabel.innerText = data.weightKg;
                          }
                          if(data.abs == 1){
                              document.getElementById("carBluetooth").innerText = "Yes";
                          }
                          else{
                              document.getElementById("carBluetooth").innerText = "No";
                          }
                          document.getElementById("carGPS").innerText = data.fuel;
                          document.getElementById("carColor").innerText = data.color;
                          document.getElementById("carType").innerText = data.type;
                          document.getElementById("carYear").innerText = data.year;
                          imagePaths = Array.isArray(data.images) ? data.images : [];
                          console.log('Image paths:', imagePaths);
                          imagePathLength = imagePaths.length;
                          console.log(imagePathLength);
                          const firstImage = imagePaths[0];
                          const imageSrc = firstImage ? "/Makina/images/motorcycles/" + firstImage : "/Makina/images/default.jpg";
                          document.getElementById("carImage").src = imageSrc;

                          updateReviewUI(data.reviewSummary, data.reviews, {
                              canReview: data.canReview,
                              message: data.reviewMessage
                          });

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

                    document.getElementById("carImage").src = "/Makina/images/motorcycles/"+imagePaths[imagePosition];

                }
            });

            document.getElementById("rightArrow").addEventListener('click', function(){
                if(imagePathLength > 0 && imagePosition + 1 < imagePathLength){
                    imagePosition++;

                    document.getElementById("carImage").src = "/Makina/images/motorcycles/"+imagePaths[imagePosition];

                }
            });

            const reviewForm = document.getElementById("reviewForm");
            if (reviewForm) {
                reviewForm.addEventListener("submit", function(event) {
                    event.preventDefault();

                    const carId = getCookie("car_id");
                    const ratingInput = document.querySelector('input[name="reviewRating"]:checked');
                    const rating = ratingInput ? ratingInput.value : "";
                    const reviewText = document.getElementById("reviewText").value.trim();
                    const noticeEl = document.getElementById("reviewNotice");

                    if (!rating || reviewText === "") {
                        if (noticeEl) {
                            noticeEl.textContent = "Please add a rating and a review.";
                        }
                        return;
                    }

                    fetch("/Makina/motorcycles/carDetails.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            action: "addReview",
                            carId: carId,
                            rating: rating,
                            reviewText: reviewText
                        })
                    })
                    .then(response => response.text())
                    .then(text => {
                        const data = safeJsonParse(text);
                        if (data.success) {
                            const selected = document.querySelector('input[name="reviewRating"]:checked');
                            if (selected) {
                                selected.checked = false;
                            }
                            document.getElementById("reviewText").value = "";
                            updateReviewUI(data.reviewSummary, data.reviews, {
                                canReview: data.canReview,
                                message: data.reviewMessage
                            });
                        } else if (noticeEl) {
                            noticeEl.textContent = data.message;
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        if (noticeEl) {
                            noticeEl.textContent = "Failed to submit review.";
                        }
                    });
                });
            }

            
