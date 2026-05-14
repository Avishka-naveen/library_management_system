
function validateForm() {
    let password=document.getElementById("password").value.trim();

    if(password.length < 8){
        alert("password must be at least 8 characters long");
        return false;
    }

}



