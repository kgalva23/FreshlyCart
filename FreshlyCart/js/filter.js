// Initialize Supabase client using the global supabase object from the CDN
const supabaseUrl = 'https://lpffpzhkeuzebucaugvw.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxwZmZwemhrZXV6ZWJ1Y2F1Z3Z3Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzE0NTA4MTQsImV4cCI6MjA0NzAyNjgxNH0.aiB2oozPPQxVKV6bxvu3sV7QjPjJZlrHOqXL3vjXUJI'; // Replace with your Supabase key
const supabase = supabase.createClient(supabaseUrl, supabaseKey);

// Fetch items from Supabase with sorting
async function fetchItemsFromSupabase(orderBy = 'price', ascending = true) {
  try {
    console.log("Fetching items from Supabase with order:", orderBy, "ascending:", ascending);
    let { data, error } = await supabase
      .from('item')
      .select('item_id, name, description, company, price, image_id')
      .order(orderBy, { ascending });

    if (error) {
      console.error('Error fetching items from Supabase:', error);
      return [];
    }

    console.log("Fetched items:", data); // Log fetched data to verify structure
    return data;
  } catch (err) {
    console.error("Unexpected error in fetchItemsFromSupabase:", err);
    return [];
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const sortSelect = document.getElementById("sortSelect");
  if (sortSelect) {
    console.log("Sort select found, adding event listener.");
    sortSelect.addEventListener("change", applyFilters);
  } else {
    console.warn("Sort select not found in the DOM.");
  }

  // Display items with default sorting on initial load
  applyFilters();
});

async function applyFilters() {
  try {
    const sortSelect = document.getElementById("sortSelect");
    if (!sortSelect) {
      console.error("Sort select element not found.");
      return;
    }

    const sortValue = sortSelect.value;
    console.log("Applying filter with sort value:", sortValue);

    // Determine sorting direction based on selected option
    const ascending = sortValue === "price_low_high";
    
    // Fetch sorted items from Supabase
    const items = await fetchItemsFromSupabase('price', ascending);
    
    // Display the items
    displayItems(items);
  } catch (err) {
    console.error("Error in applyFilters:", err);
  }
}

function displayItems(itemsToDisplay) {
  const container = document.getElementById("itemContainer");
  if (!container) {
    console.error("Item container element not found.");
    return;
  }
  container.innerHTML = "";

  itemsToDisplay.forEach(function (item) {
    const imageUrl = item.image_id 
      ? `https://your-s3-bucket-url.com/${item.image_id}.jpg` 
      : 'images/placeholder.png';

    console.log("Displaying item:", item); // Log each item to verify data

    const itemDiv = document.createElement("div");
    itemDiv.className = "col-md-6 mb-4";
    itemDiv.innerHTML = `
      <div class="card card-flex">
        <img src="${imageUrl}" alt="Item Image" height="50" width="50" class="rounded-circle">
        <div class="card-body">
          <h5 class="card-title">${item.name || 'Name Not Available'}</h5>
          <p class="card-text">${item.description || 'Description Not Available'}</p>
          <p class="card-text">Company: ${item.company || 'Not Available'}</p>
          <p class="card-text">Price: $${isNaN(Number(item.price)) ? '0.00' : Number(item.price).toFixed(2)}</p>
          <button class="btn openModalBtn" data-item-id='${item.item_id}'>Add to Cart</button>
        </div>
      </div>
    `;
    container.appendChild(itemDiv);
  });
}
