//https://element.how/crocoblock-jetsmartfilters-javascript-events/#table-of-content-1

// Listen for when the AJAX filter update is complete
window.JetSmartFilters.events.subscribe(
  "ajaxFilters/updated",
  function (provider, queryId, response) {
    console.log(
      "AJAX Filters Updated for provider:",
      provider,
      ", Query ID:",
      queryId,
      ", Response:",
      response
    );
  }
);

// Listen for the start of the AJAX filtering process
window.JetSmartFilters.events.subscribe(
  "ajaxFilters/start-loading",
  function (provider, queryID) {
    console.log(
      "AJAX Filters Start Loading for provider:",
      provider,
      ", Query ID:",
      queryID
    );
  }
);

// Listen for after the AJAX content update is complete
window.JetSmartFilters.events.subscribe(
  "ajaxFilters/end-loading",
  function (provider, queryID) {
    console.log(
      "AJAX Filters End Loading for provider:",
      provider,
      ", Query ID:",
      queryID
    );
  }
);

// Listen for when a filter is applied
window.JetSmartFilters.events.subscribe("fiter/apply", function (filterData) {
  console.log("Filter Applied:", filterData);
});

// Listen for filters being applied
window.JetSmartFilters.events.subscribe("fiters/apply", function (filterData) {
  console.log("Filters Applied:", filterData);
});

// Listen for filters being removed
window.JetSmartFilters.events.subscribe("fiters/remove", function (filterData) {
  console.log("Filters Removed:", filterData);
});

// Listen for filter change
window.JetSmartFilters.events.subscribe("fiter/change", function (filterData) {
  console.log("Filter Change:", filterData);
});

// Listen for syncing same filters
window.JetSmartFilters.events.subscribe(
  "fiter/syncSameFilters",
  function (filterData) {
    console.log("Sync Same Filters:", filterData);
  }
);

// Listen for pagination change
window.JetSmartFilters.events.subscribe(
  "pagination/change",
  function (paginationData) {
    console.log("Pagination Change:", paginationData);
  }
);

// Listen for loading more pagination
window.JetSmartFilters.events.subscribe("pagination/load-more", function () {
  console.log("Pagination Load More");
});

// Listen for active items change
window.JetSmartFilters.events.subscribe(
  "activeItems/change",
  function (activeItems, provider, queryId) {
    console.log(
      "Active Items Changed for provider:",
      provider,
      ", Query ID:",
      queryId,
      ", Active Items:",
      activeItems
    );
  }
);

// Listen for active items rebuild
window.JetSmartFilters.events.subscribe(
  "activeItems/rebuild",
  function (provider, queryId) {
    console.log(
      "Active Items Rebuild for provider:",
      provider,
      ", Query ID:",
      queryId
    );
  }
);
