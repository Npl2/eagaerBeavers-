document
  .getElementById("vehicle-registration-form")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    const vehicleDetails = {
      make: document.getElementById("make").value,
      model: document.getElementById("model").value,
      year: document.getElementById("year").value,
      vin: document.getElementById("vin").value,
      ownerName: document.getElementById("owner-name").value,
      address: document.getElementById("address").value,
      contactDetails: document.getElementById("contact-details").value,
      policyNumber: document.getElementById("policy-number").value,
      insuranceCompany: document.getElementById("insurance-company").value,
      registrationFees: document.getElementById("registration-fees").value,
    };
    console.log(vehicleDetails);
    alert("Vehicle registration submitted! Check console for details.");
  });
