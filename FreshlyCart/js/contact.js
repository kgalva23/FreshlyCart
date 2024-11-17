window.addEventListener('load', generateListeners);

function generateListeners() {
	let emailButton = document.getElementById("prefEmail");
	emailButton.addEventListener("click", function onClick() {
		document.getElementById("phone").value = "";
		document.getElementById("phone").disabled = true;
		document.getElementById("email").disabled = false;
	});
	let voiceButton = document.getElementById("voice");
	voiceButton.addEventListener("click", function onClick() {
		document.getElementById("email").value = "";
		document.getElementById("email").disabled = true;
		document.getElementById("phone").disabled = false;
	});
	let smsButton = document.getElementById("sms");
	smsButton.addEventListener("click", function onClick() {
		document.getElementById("email").value = "";
		document.getElementById("email").disabled = true;
		document.getElementById("phone").disabled = false;
	});
	let submitButton = document.getElementById("submit");
	submitButton.addEventListener("click", listInvalid);
}

function listInvalid() {
	document.getElementById("invalidList").innerHTML = "";
	let firstName = document.getElementById("fname");
	let lastName = document.getElementById("lname");
	let email = document.getElementById("email");
	let phone = document.getElementById("phone");
	let message = document.getElementById("message");
	let invalidFields = []; 

    if (!firstName.checkValidity()) {
        invalidFields.push("First name");
    }
    if (!lastName.checkValidity()) {
        invalidFields.push("Last name");
    }
    if (!email.checkValidity() && email.disabled==false) {
        invalidFields.push("Email");
    }
    if (!phone.checkValidity() && phone.disabled==false) {
        invalidFields.push("Phone Number");
    }
    if (!message.checkValidity()) {
        invalidFields.push("Message");
    }

    let invalidMessage = "Invalid fields: " + invalidFields.join(", ");
    document.getElementById("invalidList").innerHTML = invalidMessage;
}