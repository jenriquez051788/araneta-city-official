
const input1 = document.querySelector("#mobilePhone-1");

const form1 = document.querySelector("#inquiry-form-1");

const message1 = document.querySelector("#returnMessageForm1");

// Guard: the phone input only exists on the contact page. Bail out early on
// any page that doesn't have it so this script never throws on page load.
if (input1 && form1 && typeof window.intlTelInput === "function") {

  const iti1 = window.intlTelInput(input1, {
    initialCountry: "ph",
    hiddenInput: () => "full_phone",
    utilsScript: "/intl/utils.js?1706186129792" // just for formatting/placeholders etc
  });


  form1.onsubmit = () => {
    //alert("test "  + $('input1').length);
    if (!iti1.isValidNumberPrecise()) {
      if (message1) message1.innerHTML = "Invalid number. Please try again.";
      return false;
    }
    input1.value = iti1.getNumber();
  };


  const urlParams = new URLSearchParams(window.location.search);
  const fullPhone = urlParams.get('full_phone');
  if (fullPhone && message1) {
    message1.innerHTML = `Submitted hidden input value: ${fullPhone}`;
  }
}
