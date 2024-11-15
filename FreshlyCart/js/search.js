document.addEventListener("DOMContentLoaded", function () {
  var searchInput = document.getElementById("searchItems");
  if (searchInput) {
    searchInput.addEventListener("input", performSearch);
  }
});

function performSearch() {
  var searchTerm = document.getElementById("searchItems").value.toLowerCase();
  var filteredItems = items.filter(function (item) {
    return item.name.toLowerCase().includes(searchTerm);
  });
  displayItems(filteredItems);

  //JavaScript to trigger modal on button click, this shows that item has been added to cart
  document.querySelectorAll('.openModalBtn').forEach(button => {
    button.addEventListener('click', function() {
      var myModal = new bootstrap.Modal(document.getElementById('myModal'));
      myModal.show(); // Show the modal when the button is clicked
      //var itemId = this.getAttribute('data-item-id');
      addToCart(itemId);
    });    
  });
}

function displayItems(itemsToDisplay) {
  const s3url = "https://team15project.s3.us-east-2.amazonaws.com/";
  var container = document.getElementById("itemContainer");
  container.innerHTML = "";

  itemsToDisplay.forEach(function (item) {
    var itemDiv = document.createElement("div");
    itemDiv.className = "col-md-6 mb-4";
    itemDiv.innerHTML = `
            <div class="card card-flex">
                <img src='${s3url}${item.ImagePath}' class="card-img-left">
                <div class="card-body">
                    <h5 class="card-title">${item.name}</h5>
                    <p class="card-text">${item.description}</p>
                    <p class="card-text">Company: ${item.company}</p>
                    <p class="card-text">Price: $${Number(item.price).toFixed(
                      2
                    )}</p>
                    <button class="btn openModalBtn" data-item-id='${item.item_id}'>Add to Cart</button>
                </div>
            </div>
        `;
    container.appendChild(itemDiv);
  });
}
