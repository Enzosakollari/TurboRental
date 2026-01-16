<?php require_once __DIR__ . '/../config/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="./css/main.css" rel="stylesheet">
    <link href="./css/modal.css" rel="stylesheet">
  </head>
  <body>
    <?php include __DIR__ . '/../navigation/navigation.php'; ?>

    <section class="fleet-hero">
      <div class="fleet-content">
        <span class="fleet-badge">Premium Fleet</span>
        <h1>Choose the ride that fits your mood</h1>
        <p>From executive sedans to powerful SUVs, explore curated vehicles ready for every destination.</p>
      </div>
      <div class="fleet-cta">
        <button class="btn-primary" id="fleetExplore">View Available</button>
      </div>
    </section>

  <section class="filter-search-container">
    <div>
      <button id="redirectToCart">Cart</button>

      <button id="clearCart">Clear Cart</button>

      <button id="filterBtn">Filter</button>
    </div>
    <div class="search-form">
      <input type="text" autocomplete="off" placeholder="Search..." name="search" class="search-field" id="search-input">
      <button type="submit" class="search-icon">
        <i class="bi bi-search"></i> 
      </button>
    </div>
  </section>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeBtn">&times;</span>
            <h2>Filter Options</h2>
            <p>Select fuel type</p>
            <div class="checkbox-container">
              <div class="custom-checkbox">
                <input name="fuel" type="checkbox" id="benzine" value="benzine">
                <label for="benzine">Gasoline</label>
              </div>
            
              <div class="custom-checkbox">
                <input name="fuel" type="checkbox" id="nafte" value="nafte">
                <label for="nafte">Diesel</label>
              </div>
            
              <div class="custom-checkbox">
                <input name="fuel" type="checkbox" id="electric" value="elektrike">
                <label for="electric">Electric</label>
              </div>
            </div>
            
            <p>Maximum price</p>
            <div class="price-container">
              <input type="number" id="numberInput">
            </div>

            <div class="select-container">
              <p>Choose transmission:</p>
              <select id="mode" name="mode">
                <option default value="">All</option>
                <option value="automatik">Automatic</option>
                <option value="manual">Manual</option>
              </select>
            </div>
             <div class="modalBtn-container">
               <button class="modalBtn" id="applyBtn">Apply</button>
               <button class="modalBtn" id="removeBtn">Remove</button>
             </div>

        </div>
    </div>
    

    <div id="carDetailsModal" class="modal">
      <div class="carDetails">
        <div id="imageContainer">

          <button id="leftArrow"><</button>
          <img id="carImage" alt="car" >
          <button id="rightArrow">></button>

        </div>
        <div id="detailsContainer">
          <h1 id="carName"></h1>
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
              <label class="staticLabel">Seating capacity: </label>
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
          
          <button id="rezervoButton">
              ADD TO CART
          </button>
          <button id="dilButton">
              CLOSE
          </button>
        </div>

        <section class="reviews-section">
            <h2>Reviews</h2>
            <div id="reviewSummary" class="review-summary"></div>
            <div id="reviewsList" class="reviews-list"></div>
            <form id="reviewForm" class="review-form">
                <label class="staticLabel">Rating</label>
                <div class="star-rating" role="radiogroup" aria-label="Rating">
                    <input type="radio" id="star5" name="reviewRating" value="5">
                    <label for="star5" title="5 stars">★</label>
                    <input type="radio" id="star4" name="reviewRating" value="4">
                    <label for="star4" title="4 stars">★</label>
                    <input type="radio" id="star3" name="reviewRating" value="3">
                    <label for="star3" title="3 stars">★</label>
                    <input type="radio" id="star2" name="reviewRating" value="2">
                    <label for="star2" title="2 stars">★</label>
                    <input type="radio" id="star1" name="reviewRating" value="1">
                    <label for="star1" title="1 star">★</label>
                </div>
                <label class="staticLabel" for="reviewText">Your review</label>
                <textarea id="reviewText" rows="3" maxlength="500" required></textarea>
                <button id="submitReview" type="submit">Submit review</button>
            </form>
            <div id="reviewNotice" class="review-notice"></div>
        </section>
      </div>
    </div>
    <main id="fleetGrid">
    </main>

    
    <footer>
      <div class="pagination">
        <button id="prev-btn" class="disabled" disabled>Previous</button>
        <div id="page-links"></div>
        <button id="next-btn">Next</button>
      </div>
    </footer>

  </body>

  <script type="module" src="./main.js?v=3"></script>
  <script src="./carDetails.js?v=3"></script>
  <script type="module" src="./modal.js?v=3"></script>
  <script type="module" src="./filters.js?v=3"></script>
  <script src="/Makina/registerLogin/sessionTimeout.js"></script>
  <script>
    const fleetButton = document.getElementById("fleetExplore");
    if (fleetButton) {
      fleetButton.addEventListener("click", () => {
        document.getElementById("fleetGrid")?.scrollIntoView({ behavior: "smooth" });
      });
    }
  </script>
  <script>
    fetch('/Makina/makinat/main.php')
  </script>
  

  



  
</html>
