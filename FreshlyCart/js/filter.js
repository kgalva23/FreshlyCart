document.addEventListener("DOMContentLoaded", function () {
  var sortSelect = document.getElementById("sortSelect");
  if (sortSelect) {
    sortSelect.addEventListener("change", applyFilters);
  }
});

function applyFilters() {
  var sortValue = document.getElementById("sortSelect").value;
  var sortedItems;

  if (sortValue === "price_low_high") {
    sortedItems = [...items].sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
  } else if (sortValue === "price_high_low") {
    sortedItems = [...items].sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
  } else {
    sortedItems = items;
  }
  displayItems(sortedItems);

  // JavaScript to trigger modal on button click
  document.querySelectorAll('.openModalBtn').forEach(button => {
    button.addEventListener('click', function() {
      var myModal = new bootstrap.Modal(document.getElementById('myModal'));
      myModal.show(); // Show the modal when the button is clicked
      var itemId = this.getAttribute('data-item-id');
      addToCart(itemId);
    });
  });
}

function displayItems(itemsToDisplay) {
  var container = document.getElementById("itemContainer");
  container.innerHTML = "";

  itemsToDisplay.forEach(function (item) {
    var itemDiv = document.createElement("div");
    itemDiv.className = "col-md-6 mb-4";
    itemDiv.innerHTML = `
      <div class="card card-flex">
        <img src="${item.ImagePath ? item.ImagePath : 'images/apple'}" alt="Item Image" height="50" width="50" class="rounded-circle">
        <div class="card-body">
          <h5 class="card-title">${item.name}</h5>
          <p class="card-text">${item.description}</p>
          <p class="card-text">Company: ${item.company}</p>
          <p class="card-text">Price: $${Number(item.price).toFixed(2)}</p>
          <button class="btn openModalBtn" data-item-id='${item.item_id}'>Add to Cart</button>
        </div>
      </div>
    `;
    container.appendChild(itemDiv);
  });
}
