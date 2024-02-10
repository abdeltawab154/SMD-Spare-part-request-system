// script.js

// Function to handle form submission
function submitForm() {
    // Submit the form using vanilla JavaScript
    document.getElementById('itemForm').submit();
}

// Execute the form submission when the button is clicked
document.getElementById('submitBtn').addEventListener('click', submitForm);


// Function to update last submitted data table
function updateLastData(data) {
    // Clear previous data
    $("#lastDataBody").empty();

    // Create a new row with the submitted data
    var newRow = $("<tr>");
    newRow.append("<td>" + data.itemName + "</td>");
    newRow.append("<td>" + data.codeType + "</td>");
    newRow.append("<td>" + data.itemCode + "</td>");
    newRow.append("<td>" + data.description + "</td>");
    newRow.append("<td>" + data.qty + "</td>");
    newRow.append("<td>" + data.pricePerOne + "</td>");

    // Append the new row to the table body
    $("#lastDataBody").append(newRow);
}

// Execute the form submission when the button is clicked
$(document).ready(function () {
    $('#submitBtn').click(submitForm);
});
