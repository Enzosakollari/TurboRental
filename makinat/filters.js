import { cars, fetchAndDisplayCars,search, paginateCars, setCars, restorePagination, getCarDetails } from "./main.js?v=3";

export let filters = {
  fuelType: [],
  price: '',
  transmission: ''
};

const checkBoxes = document.querySelectorAll('.checkbox-container input[type="checkbox"]');
const numberInput = document.getElementById('numberInput');
const modal = document.querySelector('.modal');

checkBoxes.forEach((check) => {
  check.addEventListener('change', (event) => {
    const value = event.target.value;

    if (event.target.checked) {
      if (!filters.fuelType.includes(value)) {
        filters.fuelType.push(value);
      }
    } else {
      filters.fuelType = filters.fuelType.filter((fuel) => fuel !== value);
    }
  });
});

numberInput.addEventListener('input', (event) => {
  filters.price = event.target.value;
});

const modeSelect = document.getElementById('mode');

modeSelect.addEventListener('change', (event) => {

  filters.transmission = event.target.value;
});

var applyBtn = document.getElementById("applyBtn");
var removeBtn = document.getElementById("removeBtn");

applyBtn.onclick = async ()=>{
  restorePagination(); 
  paginateCars(search)
  modal.style.display = "none";
};

removeBtn.onclick = () => {
  removeFilters();
  modal.style.display = 'none';
};

function removeFilters() {
  checkBoxes.forEach((check) => {
    if (check.checked) check.checked = false;
  });
  numberInput.value = '';
  modeSelect.value = '';
  
  filters.fuelType = [];
  filters.price = '';
  filters.transmission = '';
  fetchAndDisplayCars();
}
