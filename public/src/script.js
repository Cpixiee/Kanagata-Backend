$(document).ready(function () {
  const apiUrl = "https://api.jikan.moe/v4/anime";
  const batchSize = 25; // Number of items per page (API default)
  const minEntries = 100; // Minimum number of entries to fetch
  let allData = []; // Array to store all fetched data
  let currentPage = 1; // Start from page 1

  // Function to fetch data from the API
  async function fetchData(page) {
    try {
      const response = await fetch(`${apiUrl}?page=${page}`);
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      const data = await response.json();
      return data.data; // Return the array of anime objects
    } catch (error) {
      console.error("Error fetching data:", error);
      return []; // Return an empty array in case of error
    }
  }

  // Function to fetch data sequentially until we have enough entries
  async function fetchAllData() {
    while (allData.length < minEntries) {
      const data = await fetchData(currentPage);
      if (data.length === 0) {
        console.log("No more data available.");
        break;
      }
      allData = allData.concat(data); // Add new data to the array
      currentPage++; // Move to the next page
      console.log(`Fetched ${data.length} entries. Total: ${allData.length}`);

      // Respect the rate limit (3 requests per second)
      await new Promise((resolve) => setTimeout(resolve, 350)); // ~350ms delay between requests
    }

    // Initialize DataTable once we have enough data
    $("#animeTable").DataTable({
      data: allData,
      columns: [
        { data: "mal_id" },
        { data: "rank" },
        { data: "title" },
        { data: "type" },
        { data: "episodes" },
        { data: "score" },
        { data: "status" },
        { data: "aired.prop.from.year" },
        { data: "aired.prop.to.year" },
      ],
    });
  }
  // Start fetching data
  fetchAllData();
});
