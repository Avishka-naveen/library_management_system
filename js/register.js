function validateRegistrationForm(){
    let userId=document.getElementById('userId').value.trim();
    let firstName=document.getElementById('firstName').value.trim();
    let lastName=document.getElementById('lastName').value.trim();
    let username=document.getElementById('username').value.trim();
    let email=document.getElementById('email').value.trim();
    let password=document.getElementById('password').value.trim();
    
    let userIdPattern = /^U\d{3}$/;
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(!userIdPattern.test(userId)){
        alert("user id must be U001 format");
        return false;
    }
    if(!emailPattern.test(email)){
        alert("please enter a valid email address");
        return false;
    }
    if(password.length <8){
        alert("password must be at least 8 characters long");
        return false;
    }
    return true;
}