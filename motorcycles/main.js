const apiUrl = '/Makina/motorcycles/main.php';
import { filters } from "./filters.js?v=3";
export let motorcycles = [];
let filteredMotorcycles = [];
let currentPage = 1;
const motorcyclesPerPage = 9;
let totalPages = 0;
let userImage;
let userId;
export let search;
const fuelLabels = { benzine: "Gasoline", nafte: "Diesel", elektrike: "Electric" };
const transmissionLabels = { automatik: "Automatic", manual: "Manual" };

function normalizeLabel(value, map) {
  if (!value) return value;
  const key = value.toString().toLowerCase();
  return map[key] || value;
}


export async function getMotorcycleDetails(filters = {}, search = '', page = 1) {
  try {
    const response = await fetch(apiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ 
        ...filters,
        search,
        page, 
        motorcyclesPerPage
      }),
    });

    const data = await response.json();

    if (data.success) {
      motorcycles = data.motorcycles;
      totalPages = data.totalPages;
      userId=data.userId;
  
      return motorcycles;
    } else {
      console.log("Error:", data.message);
      return [];
    }
  } catch (error) {
    console.error("Error fetching motorcycle details:", error);
    return [];
  }
}

export function setMotorcycles(t){
  motorcycles=t;
}

export function restorePagination(){
  currentPage=1;
}

export function displayMotorcycles(motorcyclesToDisplay) {
  const doc = document.querySelector('main');
  let html = '';
  motorcyclesToDisplay.forEach(motorcycle => {
    const imageName = motorcycle.image_path;
    const imagePath = imageName && imageName !== 'null' && imageName !== 'NULL'
      ? `/Makina/images/motorcycles/${imageName}`
      : '/Makina/images/default.jpg';
    const fuelLabel = normalizeLabel(motorcycle.fuel, fuelLabels);
    const transmissionLabel = normalizeLabel(motorcycle.transmission, transmissionLabels);
    html += `
      <div class="container" id=${motorcycle.id} onclick="showMotorcycleDetails()">
        <div class="relative">
          <div class="img-container">
            <img src="${imagePath}" alt="${motorcycle.name}" />
          </div>
          <p class="year">${motorcycle.year} / $${motorcycle.price_per_day}</p>
        </div>
        <div class="description">
          <p class="motorcycle-name">${motorcycle.name}</p>
          <div class="motorcycle-details-container">
            <i class="bi bi-lightning-charge-fill">
              <span class="text">${motorcycle.engine_cc} cc</span>
            </i>
            <i class="bi bi-fuel-pump-fill">
              <span class="text">${fuelLabel}</span>
            </i>
            <i class="bi bi-hdd-network-fill">
              <span class="text">${transmissionLabel}</span>
            </i>
          </div>
        </div>
     </div>
    </div>
  </div>
`;
});
doc.innerHTML = html;
}

export function updatePagination() {
  const prevButton = document.getElementById("prev-btn");
  const nextButton = document.getElementById("next-btn");
  const pageLinksContainer = document.getElementById("page-links");

  pageLinksContainer.innerHTML = "";

  const visiblePages = 4; 
  const startPage = Math.max(1, currentPage - Math.floor(visiblePages / 2));
  const endPage = Math.min(totalPages, startPage + visiblePages - 1);

  const adjustedStartPage = Math.max(1, endPage - visiblePages + 1);

  prevButton.disabled = currentPage === 1;
  prevButton.classList.toggle("disabled", currentPage === 1);

  if (adjustedStartPage > 1) {
    const firstPageLink = document.createElement("a");
    firstPageLink.href = "#page1";
    firstPageLink.textContent = "1";
    firstPageLink.addEventListener("click", (event) => {
      event.preventDefault();
      currentPage = 1;
      paginateMotorcycles(search, currentPage);
    });
    pageLinksContainer.appendChild(firstPageLink);

    if (adjustedStartPage > 2) {
      const ellipsis = document.createElement("span");
      ellipsis.textContent = "...";
      pageLinksContainer.appendChild(ellipsis);
    }
  }

  for (let i = adjustedStartPage; i <= endPage; i++) {
    const pageLink = document.createElement("a");
    pageLink.href = `#page${i}`;
    pageLink.textContent = i;
    pageLink.classList.toggle("active", i === currentPage);

    pageLink.addEventListener("click", (event) => {
      event.preventDefault();
      currentPage = i;
      paginateMotorcycles(search, currentPage);
    });

    pageLinksContainer.appendChild(pageLink);
  }

  if (endPage < totalPages) {
    if (endPage < totalPages - 1) {
      const ellipsis = document.createElement("span");
      ellipsis.textContent = "...";
      pageLinksContainer.appendChild(ellipsis);
    }

    const lastPageLink = document.createElement("a");
    lastPageLink.href = `#page${totalPages}`;
    lastPageLink.textContent = totalPages;
    lastPageLink.addEventListener("click", (event) => {
      event.preventDefault();
      currentPage = totalPages;
      paginateMotorcycles(search, currentPage);
    });
    pageLinksContainer.appendChild(lastPageLink);
  }

  nextButton.disabled = currentPage === totalPages;
  nextButton.classList.toggle("disabled", currentPage === totalPages);
}



export async function paginateMotorcycles(search = '', page = 1) {

  await getMotorcycleDetails(filters, search, currentPage);
  updatePagination();
  displayMotorcycles(motorcycles);
}

export async function fetchAndDisplayMotorcycles(filters = {}) {
  await getMotorcycleDetails(filters, currentPage);
  filteredMotorcycles = [];
  paginateMotorcycles();
}

document.getElementById("prev-btn").addEventListener("click", async () => {
  if (currentPage > 1) {
    currentPage--;

    await paginateMotorcycles(search, currentPage);
  }
});

document.getElementById("next-btn").addEventListener("click", async () => {
  if (currentPage < totalPages) {
    currentPage++;
    await paginateMotorcycles(search, currentPage);
  }
});




document.querySelector('.search-icon').addEventListener('click', () => {
  search = document.getElementById('search-input').value;
 
  restorePagination();
  paginateMotorcycles(search);
});

document.getElementById('search-input').addEventListener('keydown', (event) => {
  if (event.key === 'Enter') {
    search = document.getElementById('search-input').value;
   
    restorePagination();
    paginateMotorcycles(search);
    
  }
});

document.querySelector('.search-field').addEventListener('input', (event) => {
  if (event.target.value === '') {
    paginateMotorcycles();
    search='';
  }
});


fetchAndDisplayMotorcycles();

document.getElementById("redirectToCart").addEventListener("click", function(){
  window.location.href = "/Makina/pagesa/motorcycleCart.php";

})

document.getElementById("clearCart").addEventListener("click", function(){

  if(confirm("Are you sure you want to clear the cart?")){
      fetch('clearCart.php', {
          method: 'POST',
          headers: {
          'Content-Type': 'application/json',
          },
          body: JSON.stringify({ action: 'clear_cart' })
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
            alert("Cart cleared.");
          }
          else {
          alert('Failed to clear cart. Try again.');
          }
      })
      .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while clearing the cart.');
      });
  }
  
});

